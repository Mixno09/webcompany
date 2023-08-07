<?php

declare(strict_types=1);

namespace App;

use PDO;

class CityModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertCity(string $name, int $index): void
    {
        $sql = 'INSERT INTO city (name, `index`) VALUES (:name, :index)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':index', $index, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllCity(string $orderBy = 'index', string $order = 'ASC'): array
    {
        if (! in_array($orderBy, ['id', 'name', 'index'])) {
            $orderBy = 'id';
        }
        if (! in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $sql = "SELECT * FROM city ORDER BY `{$orderBy}` {$order}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($data as $item) {
            $id = $item['id'];
            $result[$id] = $item;
        }

        return $result;
    }

    public function deleteCityById(int $id): int
    {
        $sql = 'DELETE FROM city WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function updateCity(int $id, string $name, int $index): void
    {
        $sql = 'UPDATE city SET name = :name, `index` = :index WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':index', $index, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCityById(int $id): ?array
    {
        $sql = 'SELECT * FROM city WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($data) === 0) {
            return null;
        }
        return $data[0];
    }
}
