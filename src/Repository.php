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
    const ENPOINT_COLLECTION = 'collection';

    const ENDPOINT_SINGLE = 'single';

    /**
     * The name of the model.
     *
     * @var string
     */
    private $name;

    /**
     * The URL for the resource with a single result element.
     *
     * @var string
     */
    private $urlSingle;

    /**
     * The URL for the resource with a collection result element.
     *
     * @var string
     */
    private $urlCollection;

    /**
     * Fields contained in the single element response.
     *
     * @var array
     */
    private $valuesSingle = [];

    /**
     * Fields contained in the element collection response.
     *
     * @var array
     */
    private $valuesCollection = [];

    /**
     * The array of current query string parameters.
     *
     * @var array
     */
    private $queryParameters = [];

    /**
     * Fields avaialbe for filtering in the current repository.
     *
     * @var array
     */
    private $fieldsFilterable = [];

    /**
     * Fields available for sorting in the current repository.
     *
     * @var array
     */
    private $fieldsSortable = [];

    /**
     * Whether the current model requires a [Resource ID].
     *
     * @var bool
     */
    private $resourceId = true;

    /**
     * @var Client
     */
    private $client;

    /**
     * The default configuration values.
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
    public function __construct(Client $client, string $name, array $config)
    {
        $this->name = $name;

        $this->setClient($client);

        $config = \array_merge(self::$defaultConfig, $config);

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
     * @return self
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Creates a Query object.
     *
     * @return Query
     */
    public function query(): Query
    {
        return new Query($this);
    }

    /**
     * Finds a collection of models with the given parameters.
     *
     * @param array|Query $parameters
     *
     * @return Model[]
     */
    public function find($parameters): array
    {
        if ($parameters instanceof Query) {
            $parameters = $parameters->compileParameters();
        }

        $parameters = $this->formatParameters($parameters, self::ENPOINT_COLLECTION);
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
     * @param array|Query $parameters
     *
     * @return Model
     */
    public function findOne($parameters): Model
    {
        if ($parameters instanceof Query) {
            $parameters = $parameters->compileParameters();
        }

        $parameters = $this->formatParameters($parameters, self::ENDPOINT_SINGLE);
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
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function formatParameters(array $parameters, string $type): array
    {
        $this->checkQueryRepositoryCompatibility($type);
        $this->checkQueryResourceId($parameters, $type);

        $returnParameters = [];

        foreach ($parameters['query'] as $parameter => $value) {
            // The parameter is a filter,
            // so it must be checked if it is whitelisted
            if ('filter_by' === $parameter) {
                $returnParameters['filter'] = $this->queryAddFilterByParameter($value);

                continue;
            }

            // The parameter is a sorting field,
            // so it must be checked if it is whitelisted
            if ('sort_by' === $parameter) {
                $returnParameters['sort'] = $this->queryAddSortByParameter($value);

                continue;
            }

            // The parameter is a list of fields,
            // so it must be checked whether they are supported by the Repository
            if ('field_list' === $parameter) {
                $returnParameters['field_list'] = $this->queryAddFieldList($type, $value);

                continue;
            }

            // The parameter is of any other kind,
            // so it must be checked that it is supported by the Repository
            if (!\in_array($parameter, $this->queryParameters, true)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Parameter %s is not a valid query parameters for repository %s',
                    $parameter,
                    $this->name
                ));
            }
            if (!\is_string($value) && !\is_numeric($value)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Value for parameter %s is not a valid type, must be either a string or a number, %s given',
                    $parameter,
                    \gettype($value)
                ));
            }
            $returnParameters[$parameter] = $value;
        }

        return [
            'parameters' => $returnParameters,
            'resource_id' => $parameters['resource_id'],
        ];
    }

    /**
     * Checks if the parameters are whitelisted and creates the URL string.
     *
     * @param array $values
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function queryAddFilterByParameter(array $values): string
    {
        $filters = [];
        foreach ($values as $name => $value) {
            if (!\in_array($name, $this->fieldsFilterable, true)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Parameter %s is not available for filtering in repository %s',
                    $name,
                    $this->name
                ));
            }
            $filters[] = $name.':'.\rawurldecode($value);
        }

        return \implode(',', $filters);
    }

    /**
     * Checks if the field is sortable and creates the URL string.
     *
     * @param array $value
     *
     * @return string
     */
    private function queryAddSortByParameter(array $value): string
    {
        if (!\in_array($value[0], $this->fieldsSortable, true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Parameter %s is not available for sorting in repository %s',
                $value[0],
                $this->name
            ));
        }

        return $value[0].':'.$value[1];
    }

    /**
     * Checks if the values are in the repository's field list and creates the URL string.
     *
     * @param string $type
     * @param array  $value
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function queryAddFieldList(string $type, array $value): string
    {
        $fieldList = [];

        $values = self::ENDPOINT_SINGLE === $type ? $this->valuesSingle : $this->valuesCollection;
        foreach ($value as $parameter) {
            if (!\in_array($parameter, $values, true)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Field %s is not available in the field list for repository %s, try one of %s',
                    $parameter,
                    $this->name,
                    \implode(', ', $values)
                ));
            }

            $fieldList[] = $parameter;
        }

        return \implode(',', $fieldList);
    }

    /**
     * Checks that the given Query type is compatible with the Repository.
     *
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    private function checkQueryRepositoryCompatibility(string $type): void
    {
        if (self::ENDPOINT_SINGLE === $type && !$this->urlSingle) {
            throw new \InvalidArgumentException(\sprintf(
                'Trying to perform a single-type query on repository %s, which supports only collection type',
                $this->name
            ));
        }
        if (self::ENPOINT_COLLECTION === $type && !$this->urlCollection) {
            throw new \InvalidArgumentException(\sprintf(
                'Trying to perform a collection-type query on repository %s, which supports only single type',
                $this->name
            ));
        }
    }

    /**
     * Finds possible problems with the presence of a resource ID, or lack thereof.
     *
     * @param array  $parameters
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    private function checkQueryResourceId(array $parameters, string $type): void
    {
        if (!$this->resourceId && $parameters['resource_id']) {
            throw new \InvalidArgumentException(\sprintf(
                'Trying to query with a resource ID, but the repository %s does not provide support for them',
                $this->name
            ));
        }
        if (self::ENDPOINT_SINGLE === $type && !$parameters['resource_id']) {
            throw new \InvalidArgumentException(\sprintf(
                'Trying to query a single element without providing a resource ID for repository %s',
                $this->name
            ));
        }
        if (self::ENPOINT_COLLECTION === $type && $parameters['resource_id']) {
            throw new \InvalidArgumentException(\sprintf(
                'Trying to query a collection element by providing a resource ID for repository %s',
                $this->name
            ));
        }
    }
}
