<?php

namespace App\Services;

/**
 * Class BaseService
 * @package App\Services
 */
class BaseService implements Service
{
    /**
     * @var
     */
    protected $repository;

    /**
     * BaseService constructor.
     * @param $repository
     */
    public function __construct($repository = null)
    {   
        $this->repository = $repository;
    }

    /**
     * @param string $connection
     * @param array|null $where
     * @param array|null $whereNot
     * @param array|null $with
     * @param null $orderBy
     * @param int|null $limit
     * @return mixed
     */
    public function all(
        string $connection,
        array $where = null,
        array $whereNot = null,
        array $with = null,
        $orderBy = null,
        int $limit = null
    )
    {
        return $this->repository->all($connection, $where, $whereNot, $with, $orderBy, $limit);
    }

    /**
     * @param string $connection
     * @param array $data
     * @return mixed
     */
    public function create(
        string $connection,
        array $data
    )
    {
        return $this->repository->create($connection, $data);
    }

    /**
     * @param string $connection
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(
        string $connection,
        int $id,
        array $data
    ): bool
    {
        return $this->repository->update($connection, $id, $data);
    }

    /**
     * @param string $connection
     * @param int $id
     * @param string $column
     * @return bool
     */
    public function increment(
        string $connection,
        int $id,
        string $column
    ): bool
    {
        return $this->repository->increment($connection, $id, $column);
    }

    /**
     * @param string $connection
     * @param int $id
     */
    public function delete(
        string $connection,
        int $id
    ): void
    {
        $this->repository->softDelete($connection, $id);
    }

    /**
     * @param string $connection
     * @param int $id
     */
    public function softDelete(
        string $connection,
        int $id
    ): void
    {
        $this->repository->softDelete($connection, $id);
    }

    /**
     * @param string $connection
     * @param int $id
     */
    public function hardDelete(
        string $connection,
        int $id
    ): void
    {
        $this->repository->hardDelete($connection, $id);
    }

    /**
     * @param string $connection
     * @param array $where
     * @param array|null $with
     * @param null $orderBy
     * @return mixed
     */
    public function show(
        string $connection,
        array $where,
        array $with = null,
        $orderBy = null
    )
    {
        return $this->repository->show($connection, $where, $with, $orderBy);
    }

    /**
     * @param string $connection
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function showByName(
        string $connection,
        string $column,
        string $value
    )
    {
        return $this->repository->showByName($connection, $column, $value);
    }
}
