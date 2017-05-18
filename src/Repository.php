<?php

/**
 * This file is part of the GiantBomb PHP API created by Davide Borsatto.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2017, Davide Borsatto
 */
namespace DBorsatto\GiantBomb;

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
    private $name = null;

    /**
     * The URL for the resource with a single result element.
     *
     * @var string
     */
    private $urlSingle = null;

    /**
     * The URL for the resource with a collection result element.
     *
     * @var string
     */
    private $urlCollection = null;

    /**
     * Fields contained in the single element response.
     *
     * @var array
     */
    private $valuesSingle;

    /**
     * Fields contained in the element collection response.
     *
     * @var array
     */
    private $valuesCollection;

    /**
     * The array of current query string parameters.
     *
     * @var array
     */
    private $queryParameters;

    /**
     * Fields avaialbe for filtering in the current repository.
     *
     * @var array
     */
    private $fieldsFilterable;

    /**
     * Fields available for sorting in the current repository.
     *
     * @var array
     */
    private $fieldsSortable;

    /**
     * Whether the current model requires a [Resource ID].
     *
     * @var bool
     */
    private $resourceId;

    /**
     * @var Client
     */
    private $client;

    /**
     * The default configuration values
     *
     * @var array
     */
    private static $defaultConfig = [
        'values_single' => [],
        'values_collection' => [],
        'query_parameters' => [],
        'fields_filterable' => [],
        'fields_sortable' => [],
        'resource_id' => true,
    ];

    /**
     * Class constructor.
     *
     * @param Client $client
     * @param string $name
     * @param array  $config
     */
    public function __construct(Client $client = null, $name, $config)
    {
        $this->name = $name;

        $this->setClient($client);

        $config += self::$defaultConfig;

        $this->urlSingle = $config['url_single'];
        $this->urlCollection = $config['url_collection'];

        $this->valuesSingle = $config['values_single'];
        $this->valuesCollection = $config['values_collection'];

        $this->queryParameters = $config['query_parameters'];

        $this->fieldsFilterable = $config['fields_filterable'];
        $this->fieldsSortable = $config['fields_sortable'];

        $this->resourceId = $config['resource_id'];
    }

    /**
     * Sets the current Client object.
     *
     * @param Client $client
     *
     * @return Repository
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
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
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
    public function find($parameters)
    {
        if ($parameters instanceof Query) {
            $parameters = $parameters->compileParameters();
        }

        $parameters = $this->formatParameters($parameters, 'collection');
        $url = $this->urlCollection.'/';

        $results = $this->client->loadResource($url, $parameters['parameters']);

        $models = [];
        foreach ($results as $result) {
            $models[] = new Model($this->name, $result);
        }

        return $models;
    }
	
	/**
	 * Finds a single model with the given parameters.
	 *
	 * @param array $parameters
	 *
	 * @return \DBorsatto\GiantBomb\Model
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
    public function findOne($parameters)
    {
        if ($parameters instanceof Query) {
            $parameters = $parameters->compileParameters();
        }

        $parameters = $this->formatParameters($parameters, 'single');
        $url = $this->urlSingle.'/'.$parameters['resource_id'].'/';

        $result = $this->client->loadResource($url, $parameters['parameters']);

        return new Model($this->name, $result);
    }
	
	/**
	 * Formats and performs checks on the given parameters.
	 *
	 * @param array  $parameters
	 * @param string $type
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
    private function formatParameters($parameters, $type)
    {
        // Checks that the given Query is compatible with the Repository
        if ($type === 'single' && !$this->urlSingle) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to perform a single-type query on repository %s, which supports only collection type',
                $this->name
            ));
        }
        if ($type === 'collection' && !$this->urlCollection) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to perform a collection-type query on repository %s, which supports only single type',
                $this->name
            ));
        }

        // Finds possible problems with the presence of a resource ID, or lack thereof
        if (!$this->resourceId && $parameters['resource_id']) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to query with a resource ID, but the repository %s does not provide support for them',
                $this->name
            ));
        }
        if ($type === 'single' && !$parameters['resource_id']) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to query a single element without providing a resource ID for repository %s',
                $this->name
            ));
        }
        if ($type === 'collection' && $parameters['resource_id']) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to query a collection element by providing a resource ID for repository %s',
                $this->name
            ));
        }

        $returnParameters = [];

        foreach ($parameters['query'] as $parameter => $value) {
            // The parameter is a filter, so it must be checked if it is whitelisted
            if ($parameter === 'filter_by') {
                $returnParameters['filter'] = [];
                foreach ($value as $filterName => $filterValue) {
                    if (!in_array($filterName, $this->fieldsFilterable, false)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Parameter %s is not available for filtering in repository %s',
                            $filterName,
                            $this->name
                        ));
                    }
                    $returnParameters['filter'][] = $filterName.':'.rawurldecode($filterValue);
                }
                $returnParameters['filter'] = implode(',', $returnParameters['filter']);

                continue;
            }

            // The parameter is a sorting field, so it must be checked if it is whitelisted
            if ($parameter === 'sort_by') {
                if (!in_array($value[0], $this->fieldsSortable, false)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Parameter %s is not available for sorting in repository %s',
                        $value[0],
                        $this->name
                    ));
                }
                $returnParameters['sort'] = $value[0].':'.$value[1];

                continue;
            }

            // The parameter is a list of fields, so it must be checked whether they are supported by the Repository
            if ($parameter === 'field_list') {
                $returnParameters['field_list'] = [];
                $values = $type === 'single' ? $this->valuesSingle : $this->valuesCollection;
                foreach ($value as $param) {
                    if (!in_array($param, $values, false)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Field %s is not available in the field list for repository %s, try one of %s',
                            $param,
                            $this->name,
                            implode(', ', $values)
                        ));
                    }
                    $returnParameters['field_list'][] = $param;
                }
                $returnParameters['field_list'] = implode(',', $returnParameters['field_list']);

                continue;
            }

            // The parameter is of any other kind, so it must be checked that it is supported by the Repository
            if (!in_array($parameter, $this->queryParameters, false)) {
                throw new \InvalidArgumentException(sprintf(
                    'Parameter %s is not a valid query parameters for repository %s',
                    $parameter,
                    $this->name
                ));
            }
            if (!is_string($value) && !is_numeric($value)) {
                throw new \InvalidArgumentException(sprintf(
                    'Value for parameter %s is not a valid type, must be either a string or a number, %s given',
                    $parameter,
                    gettype($value)
                ));
            }
            $returnParameters[$parameter] = $value;
        }

        return [
            'parameters' => $returnParameters,
            'resource_id' => $parameters['resource_id'],
        ];
    }
}
