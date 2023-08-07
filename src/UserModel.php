<?php

declare(strict_types=1);

namespace App;

use PDO;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertUser(string $name, string $surName, int $cityId, string $fileName): void
    {
        $sql = 'INSERT INTO user (name, surname, cityId, fileName) VALUES (:name, :surname, :cityId, :fileName)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':surname', $surName);
        $stmt->bindValue(':cityId', $cityId, PDO::PARAM_INT);
        $stmt->bindValue(':fileName', $fileName);
        $stmt->execute();
    }

    public function updateUser(int $id, string $name, string $surName, int $cityId, ?string $fileName = null): void
    {
        $sql = 'UPDATE user SET name = :name, surname = :surname, cityid = :cityId';
        if ($fileName !== null) {
            $sql .= ', fileName = :fileName';
        }
        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':surname', $surName);
        $stmt->bindValue(':cityId', $cityId, PDO::PARAM_INT);
        if ($fileName !== null) {
            $stmt->bindValue(':fileName', $fileName);
        }
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    public function getAllUser(string $orderBy = 'id', string $order = 'ASC'): array
    {
        if (! in_array($orderBy, ['id', 'name', 'surname', 'cityid'])) {
            $orderBy = 'id';
        }
        if (! in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $sql = "SELECT * FROM user ORDER BY {$orderBy} {$order}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $id): ?array
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($data) === 0) {
            return null;
        }
        return $data[0];
    }

    public function getUsersByCityId(int $id): array
    {
        $sql = 'SELECT * FROM user WHERE cityid = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUsersBySearch(string $search): array
    {
        $sql = 'SELECT * FROM user WHERE name LIKE :search OR surname LIKE :search';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_NAMED);
    }

    public function deleteUserById(int $id): int
    {
        $sql = 'DELETE FROM user WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
