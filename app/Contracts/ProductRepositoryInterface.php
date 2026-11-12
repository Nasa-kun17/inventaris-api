<?php

namespace App\Contracts;

interface ProductRepositoryInterface
{
    public function getAll(array $filters = []): array;
    public function count(array $filters = []): int;
    public function findById(int $id): ?array;
    public function create(array $data): array;
    public function update(int $id, array $data): ?array;
    public function updateStock(int $id, int $newStock): ?array;
    public function delete(int $id): bool;
}