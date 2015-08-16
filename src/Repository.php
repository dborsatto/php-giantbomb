<?php

/**
 * This file is part of the GiantBomb PHP API created by Davide Borsatto.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2015, Davide Borsatto
 */
namespace dborsatto\GiantBomb;

/**
 * Class Repository.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
class Repository
{
    /**
     * The name of the model.
     *
     * @var string
     */
    protected $name = null;

    /**
     * The URL for the resource with a single result element.
     *
     * @var string
     */
    protected $urlSingle = null;

    /**
     * The URL for the resource with a collection result element.
     *
     * @var string
     */
    protected $urlCollection = null;

    /**
     * Fields contained in the single element response.
     *
     * @var array
     */
    protected $valuesSingle = array();

    /**
     * Fields contained in the element collection response.
     *
     * @var array
     */
    protected $valuesCollection = array();

    /**
     * The array of current query string parameters.
     *
     * @var array
     */
    protected $queryParameters = array();

    /**
     * Fields avaialbe for filtering in the current repository.
     *
     * @var array
     */
    protected $fieldsFilterable = array();

    /**
     * Fields available for sorting in the current repository.
     *
     * @var array
     */
    protected $fieldsSortable = array();

    /**
     * Whether the current model requires a [Resource ID].
     *
     * @var bool
     */
    protected $needsResourceId = true;

    /**
     * @var Manager
     */
    protected $manager = null;

    /**
     * Class constructor.
     *
     * @param Manager $manager
     * @param string  $name
     * @param array   $config
     */
    public function __construct(Manager $manager, $name, $config)
    {
        $this->manager = $manager;

        $this->name = $name;

        $this->urlSingle = $config['url_single'];
        $this->urlCollection = $config['url_collection'];

        $this->valueSingle = $config['values_single'];
        $this->valuesCollection = $config['values_collection'];

        $this->queryParameters = $config['query_parameters'];

        $this->fieldsFilterable = $config['fields_filterable'];
        $this->fieldsSortable = $config['fields_sortable'];

        $this->resourceId = $config['resource_id'];
    }

    /**
     * Tells whether the given field is available for filtering.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isValidFilteringField($field)
    {
        return in_array($field, $this->fieldsFilterable);
    }

    /**
     * Tells whether the given field is available for sorting.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isValidSortingField($field)
    {
        return in_array($field, $this->fieldsSortable);
    }

    /**
     * Tells whether the given field is a valid parameter.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isValidParameter($field)
    {
        return in_array($field, $this->queryParameters);
    }

    /**
     * Creates a Query object.
     *
     * @return Query
     */
    public function query()
    {
        return new Query($this);
    }

    /**
     * Finds a collection of models with the given parameters.
     *
     * @param array $parameters
     *
     * @return array
     */
    public function find(array $parameters)
    {
        $parameters = $this->formatParameters($parameters, 'collection');
        $url = $this->urlCollection.'/';

        $results = $this->manager->loadResource($url, $parameters['parameters'], 'collection');

        $models = array();
        foreach ($results as $result) {
            $models[] = $this->buildModel($result);
        }

        return $models;
    }

    /**
     * Finds a single model with the given parameters.
     *
     * @param array $parameters
     *
     * @return Model
     */
    public function findOne(array $parameters)
    {
        $parameters = $this->formatParameters($parameters, 'single');
        $url = $this->urlSingle.'/'.$parameters['resource_id'].'/';

        $result = $this->manager->loadResource($url, $parameters['parameters'], 'single');

        return $this->buildModel($result);
    }

    /**
     * Formats and performs checks on the given parameters.
     *
     * @param array  $parameters
     * @param string $type
     *
     * @return array
     */
    private function formatParameters($parameters, $type)
    {
        if ($type == 'single' and !$this->urlSingle) {
            throw new \InvalidArgumentException(sprintf('Trying to perform a single-type query on repository %s, which supports only collection type', $this->name));
        }
        if ($type == 'collection' and !$this->urlCollection) {
            throw new \InvalidArgumentException(sprintf('Trying to perform a collection-type query on repository %s, which supports only single type', $this->name));
        }

        if (!$this->needsResourceId and $parameters['resource_id']) {
            throw new \InvalidArgumentException(sprintf('Trying to query with a resource ID, but the repository %s does not provide support for them', $this->name));
        }
        if ($type == 'single' and !$parameters['resource_id']) {
            throw new \InvalidArgumentException(sprintf('Trying to query a single element without providing a resource ID for repository %s', $this->name));
        }

        $returnParameters = array();

        foreach ($parameters['query'] as $parameter => $value) {
            if ($parameter == 'filter_by') {
                $returnParameters['filter'] = array();
                foreach ($value as $filterName => $filterValue) {
                    if (!in_array($filterName, $this->fieldsFilterable)) {
                        throw new \InvalidArgumentException(sprintf('Parameter %s is not available for filtering in repository %s', $filterName, $this->name));
                    }
                    $returnParameters['filter'][] = $filterName.':'.rawurldecode($filterValue);
                }
                $returnParameters['filter'] = implode(',', $returnParameters['filter']);

                continue;
            }

            if ($parameter == 'sort_by') {
                if (!in_array($value[0], $this->fieldsSortable)) {
                    throw new \InvalidArgumentException(sprintf('Parameter %s is not available for sorting in repository %s', $value[0], $this->name));
                }
                $returnParameters['sort'] = $value[0].':'.$value[1];

                continue;
            }

            if ($parameter == 'field_list') {
                $returnParameters['field_list'] = array();
                $values = $type == 'single' ? $this->valuesSingle : $this->valuesCollection;
                foreach ($value as $parameter) {
                    if (!in_array($parameter, $values)) {
                        throw new \InvalidArgumentException(sprintf('Field %s is not available in the field list for repository %s, try one of %s', $parameter, $this->name, implode(', ', $values)));
                    }
                    $returnParameters['field_list'][] = $parameter;
                }
                $returnParameters['field_list'] = implode(',', $returnParameters['field_list']);

                continue;
            }

            if (!in_array($parameter, $this->queryParameters)) {
                throw new \InvalidArgumentException(sprintf('Parameter %s is not a valid query parameters for repository %s', $parameter, $this->name));
            }
            if (!is_string($value) and !is_numeric($value)) {
                throw new \InvalidArgumentException(sprintf('Value for parameter %s is not a valid type, must be either a string or a number, %s given', $parameter, gettype($value)));
            }
            $returnParameters[$parameter] = $value;
        }

        return array(
            'parameters' => $returnParameters,
            'resource_id' => $parameters['resource_id'],
        );
    }

    /**
     * Builds a Model object with the given data.
     *
     * @param array $data
     *
     * @return Model
     */
    private function buildModel($data)
    {
        $model = new Model();
        $model->initialize($this->name, $data);

        return $model;
    }
}
