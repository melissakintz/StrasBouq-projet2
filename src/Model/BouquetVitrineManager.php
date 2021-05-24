<?php

namespace App\Model;

class BouquetVitrineManager extends AbstractManager
{

    public const TABLE = 'bouquetVitrine';
    public const TABLE_2 = 'stock_bouquetVitrine';
    public const TABLE_3 = 'stock';

    public function insert(array $stock): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "(name, image ) VALUES (:name, :image)");
        $statement->bindValue('name', $stock['name'], \PDO::PARAM_STR);
        $statement->bindValue('image', $stock['image'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function insertStockBouquetVitrine(array $bouquetVitrine): void
    {
        $query = "INSERT INTO " . self::TABLE_2 . " 
        VALUES(" . $bouquetVitrine['idStock'] . "," . $bouquetVitrine['bouquetV_id'] . ")";
        $this->pdo->exec($query);
    }

    public function update(array $stock): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name , `image` = :image
         WHERE id=:id");
        $statement->bindValue('id', $stock['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $stock['name'], \PDO::PARAM_STR);
        $statement->bindValue('image', $stock['image'], \PDO::PARAM_STR);

        return  $statement->execute();
    }

    public function showBouquet(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT *, count(sbv.stock_id) as number
        FROM " . self::TABLE_2 . " sbv  
        JOIN " . self:: TABLE_3 . " s 
        ON s.id=sbv.stock_id WHERE sbv.bouquetVitrine_id=:id GROUP BY sbv.stock_id  ");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function showBouquetPanier(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT *
        FROM " . self::TABLE_2 . " sbv  
        JOIN " . self:: TABLE_3 . " s 
        ON s.id=sbv.stock_id WHERE sbv.bouquetVitrine_id=:id ");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectLastId()
    {
        $statement = $this->pdo->query("SELECT max(id) FROM " . self::TABLE);

        return $statement->fetch(\PDO::FETCH_NUM);
    }

    public function deleteFleur(int $id, $bouquet): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE_2 . " 
        WHERE stock_id =:id 
        AND bouquetVitrine_id=:bouquet LIMIT 1");

            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->bindValue('bouquet', $bouquet, \PDO::PARAM_INT);

            $statement->execute();
    }

    public function showPriceBouquet()
    {
        $statement = $this->pdo->prepare("SELECT sbv.bouquetVitrine_id,bv.name,bv.image, sum(s.price) as total
        FROM " . self::TABLE_2 . " sbv 
        JOIN " . self::TABLE_3 . " s 
        ON s.id=sbv.stock_id 
        JOIN bouquetVitrine bv
        ON bv.id=sbv.bouquetVitrine_id
        GROUP BY bouquetVitrine_id");

        $statement->execute();

        return $statement->fetchAll();
    }
}
