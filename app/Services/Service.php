<?php

namespace App\Services;

/**
 * Interface Service
 * @package App\Services
 */
interface Service
{
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
    );

    /**
     * @param string $connection
     * @param array $data
     * @return mixed
     */
    public function create(
        string $connection,
        array $data
    );

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
    ): bool;

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
    ): bool;

    /**
     * @param string $connection
     * @param int $id
     */
    public function delete(
        string $connection,
        int $id
    ): void;

    /**
     * @param string $connection
     * @param int $id
     */
    public function softDelete(
        string $connection,
        int $id
    ): void;

    /**
     * @param string $connection
     * @param int $id
     */
    public function hardDelete(
        string $connection,
        int $id
    ): void;

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
    );

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
    );
}
