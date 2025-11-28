<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface BaseRepositoryInterface
 *
 * Base repository interface that defines common database operations.
 * All repository interfaces should extend this interface.
 *
 * @package App\Repositories\Contracts
 */
interface BaseRepositoryInterface
{
    /**
     * Get all records from the database.
     *
     * @param array $columns Columns to select
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get paginated records.
     *
     * @param int $perPage Number of items per page
     * @param array $columns Columns to select
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int $id Record ID
     * @param array $columns Columns to select
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by its ID or throw an exception.
     *
     * @param int $id Record ID
     * @param array $columns Columns to select
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Create a new record in the database.
     *
     * @param array $data Data to create
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing record.
     *
     * @param int $id Record ID
     * @param array $data Data to update
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a record by its ID.
     *
     * @param int $id Record ID
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find records by specific criteria.
     *
     * @param array $criteria Search criteria
     * @param array $columns Columns to select
     * @return Collection
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection;
}
