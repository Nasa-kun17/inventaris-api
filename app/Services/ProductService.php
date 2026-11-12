<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Exceptions\NotFoundException;
use InvalidArgumentException;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): array
    {
        $page  = max(1, (int) ($filters['page'] ?? 1));
        $limit = max(1, min(100, (int) ($filters['limit'] ?? 10)));

        $items = $this->repository->getAll(array_merge($filters, [
            'page'  => $page,
            'limit' => $limit,
        ]));
        $total = $this->repository->count($filters);

        return [
            'items' => $items,
            'meta' => [
                'page'        => $page,
                'limit'       => $limit,
                'total'       => $total,
                'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
            ],
        ];
    }

    public function getById(int $id): array
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new NotFoundException('Produk tidak ditemukan');
        }

        return $product;
    }

    public function create(array $data): array
    {
        if (empty(trim($data['name'] ?? ''))) {
            throw new InvalidArgumentException('Nama produk wajib diisi');
        }

        if (!isset($data['category_id']) ||
            filter_var($data['category_id'], FILTER_VALIDATE_INT) === false ||
            (int) $data['category_id'] < 1) {
            throw new InvalidArgumentException('Kategori tidak valid');
        }

        if (!isset($data['price']) ||
            !is_numeric($data['price']) ||
            !is_finite((float) $data['price']) ||
            (float) $data['price'] < 0) {
            throw new InvalidArgumentException('Harga harus berupa angka nonnegatif');
        }

        if (!isset($data['stock']) ||
            filter_var($data['stock'], FILTER_VALIDATE_INT) === false ||
            (int) $data['stock'] < 0) {
            throw new InvalidArgumentException('Stok harus berupa bilangan bulat nonnegatif');
        }

        return $this->repository->create([
            'category_id' => (int) $data['category_id'],
            'name'        => trim($data['name']),
            'price'       => (float) $data['price'],
            'stock'       => (int) $data['stock'],
        ]);
    }

    public function update(int $id, array $data): array
    {
        $this->getById($id);

        if (empty(trim($data['name'] ?? ''))) {
            throw new InvalidArgumentException('Nama produk wajib diisi');
        }

        if (!isset($data['category_id']) ||
            filter_var($data['category_id'], FILTER_VALIDATE_INT) === false ||
            (int) $data['category_id'] < 1) {
            throw new InvalidArgumentException('Kategori tidak valid');
        }

        if (!isset($data['price']) ||
            !is_numeric($data['price']) ||
            !is_finite((float) $data['price']) ||
            (float) $data['price'] < 0) {
            throw new InvalidArgumentException('Harga tidak valid');
        }

        if (!isset($data['stock']) ||
            filter_var($data['stock'], FILTER_VALIDATE_INT) === false ||
            (int) $data['stock'] < 0) {
            throw new InvalidArgumentException('Stok tidak valid');
        }

        return $this->repository->update($id, [
            'category_id' => (int) $data['category_id'],
            'name'        => trim($data['name']),
            'price'       => (float) $data['price'],
            'stock'       => (int) $data['stock'],
        ]);
    }

    public function delete(int $id): void
    {
        $this->getById($id);
        $this->repository->delete($id);
    }

    public function reduceStock(int $id, int $qty): array
    {
        if ($qty < 1) {
            throw new InvalidArgumentException('Jumlah pengurangan stok harus lebih dari 0');
        }

        $product = $this->getById($id); // otomatis lempar 404 jika tidak ada

        if ($product['stock'] < $qty) {
            throw new InvalidArgumentException('Stok tidak mencukupi untuk pengurangan sebanyak ' . $qty);
        }

        return $this->repository->updateStock($id, $product['stock'] - $qty);
    }
}