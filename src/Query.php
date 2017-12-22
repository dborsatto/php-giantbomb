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
 * Class Query.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
class Query
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * A list of active filter.
     *
     * @var array
     */
    private $filterBy = [];

    /**
     * The active sorting field.
     *
     * @var string[]
     */
    private $sortBy = [];

    /**
     * A list of fields that will be loaded.
     *
     * @var array
     */
    private $fieldList = [];

    /**
     * The repository resource ID.
     *
     * @var string
     */
    private $resourceId;

    /**
     * A list of active parameters.
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Class constructor.
     *
     * @param Repository|null $repository
     */
    public function __construct(Repository $repository = null)
    {
        if ($repository) {
            $this->setRepository($repository);
        }
    }

    /**
     * Sets the current Repository.
     *
     * @param Repository|null $repository
     *
     * @return self
     */
    public function setRepository(Repository $repository = null): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Adds a field to the current filtering set.
     *
     * @param string $field
     * @param string $value
     *
     * @return self
     */
    public function addFilterBy(string $field, string $value): self
    {
        $this->filterBy[$field] = $value;

        return $this;
    }

    /**
     * Sorts by the given value.
     *
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function sortBy(string $field, string $direction = 'asc'): self
    {
        $this->sortBy = [$field, $direction];

        return $this;
    }

    /**
     * Sets the field list to be included in the result.
     *
     * @param array $list
     *
     * @return self
     */
    public function setFieldList(array $list): self
    {
        $this->fieldList = $list;

        return $this;
    }

    /**
     * Sets a parameter for the current query.
     *
     * @param string $parameter
     * @param string $value
     *
     * @return self
     */
    public function setParameter(string $parameter, string $value): self
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    /**
     * Sets a resource ID for the current query.
     *
     * @param string $resourceId
     *
     * @return self
     */
    public function setResourceId(string $resourceId): self
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * Loads an array of resource Model given the current data.
     *
     * @param Repository|null $repository
     *
     * @throws \RuntimeException
     *
     * @return Model[]
     */
    public function find(Repository $repository = null): array
    {
        if (!$repository && !($repository = $this->repository)) {
            throw new \RuntimeException('The current Query object is not tied to any Repository');
        }

        return $repository->find($this->compileParameters());
    }

    /**
     * Loads a single resource Model given the current data.
     *
     * @param Repository|null $repository
     *
     * @throws \RuntimeException
     *
     * @return Model
     */
    public function findOne(Repository $repository = null): Model
    {
        if (!$repository && !($repository = $this->repository)) {
            throw new \RuntimeException('The current Query object is not tied to any Repository');
        }

        return $repository->findOne($this->compileParameters());
    }

    /**
     * Returns an array of the current parameters.
     *
     * @return array
     */
    public function compileParameters(): array
    {
        $return = [
            'query' => [],
            'resource_id' => $this->resourceId,
        ];

        if ($this->filterBy) {
            $return['query']['filter_by'] = $this->filterBy;
        }

        if ($this->sortBy) {
            $return['query']['sort_by'] = $this->sortBy;
        }

        if ($this->fieldList) {
            $return['query']['field_list'] = $this->fieldList;
        }

        if ($this->parameters) {
            foreach ($this->parameters as $name => $value) {
                $return['query'][$name] = $value;
            }
        }

        return $return;
    }
}
