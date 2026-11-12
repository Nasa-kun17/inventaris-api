<?php
namespace App\Services;
use App\Contracts\CategoryRepositoryInterface;
use App\Exceptions\NotFoundException;
use InvalidArgumentException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): array
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            throw new NotFoundException('Kategori tidak ditemukan');
        }
        return $category;
    }

    public function create(array $data): array
    {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new InvalidArgumentException('Nama kategori wajib diisi');
        }
        return $this->repository->create([
            'name' => $name,
            'description' => $data['description'] ?? null
        ]);
    }

    public function update(int $id, array $data): array
    {
        $this->getById($id);
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new InvalidArgumentException('Nama kategori wajib diisi');
        }
        return $this->repository->update($id, [
            'name' => $name,
            'description' => $data['description'] ?? null
        ]);
    }

    public function delete(int $id): void
    {
        $this->getById($id);
        $this->repository->delete($id);
    }
}