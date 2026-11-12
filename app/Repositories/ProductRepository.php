<?php

namespace App\Repositories;

use PDO;
use App\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private PDO $db) {}

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = (int) $filters['category_id'];
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $limit  = max(1, min(100, (int) ($filters['limit'] ?? 10)));
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM products WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = (int) $filters['category_id'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO products (category_id, name, price, stock)
                VALUES (:category_id, :name, :price, :stock)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->findById((int) $this->db->lastInsertId());
    }

    public function update(int $id, array $data): ?array
    {
        $sql = "UPDATE products
                SET category_id = :category_id,
                    name = :name,
                    price = :price,
                    stock = :stock
                WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->findById($id);
    }

    public function updateStock(int $id, int $newStock): ?array
    {
        $stmt = $this->db->prepare(
            "UPDATE products SET stock = :stock WHERE id = :id"
        );
        $stmt->execute(['stock' => $newStock, 'id' => $id]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}