<?php

namespace App\Model;

class BouquetCustomerManager extends AbstractManager
{
    public const TABLE = 'bouquetCustomer';
    public const TABLE_2 = 'stock_bouquetCustomer';
    public const TABLE_3 = 'stock';
    /**
     * Insert new item in database.
     */
    public function insert(array $bouquetCustomer)
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' VALUES (null, :customer_id, :name)');
        $statement->bindValue('customer_id', $bouquetCustomer['customer_id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $bouquetCustomer['name'], \PDO::PARAM_STR);

        $statement->execute();
    }

    public function insertFlowersInBouquet($stock, $bouquet)
    {
            $query = "INSERT INTO " . self::TABLE_2 . " VALUES( 
                " . $stock . ", " . $bouquet . ")";
            $this->pdo->exec($query);
    }
    /**
     * Update item in database.
     */
    public function update(array $bouquetCustomer): bool
    {
        $statement = $this->pdo->prepare('UPDATE ' . self::TABLE . ' SET `name` = :name WHERE id=:id');
        $statement->bindValue('id', $bouquetCustomer['bouquet_id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $bouquetCustomer['name'], \PDO::PARAM_STR);
        return $statement->execute();
    }

    public function selectBouquetCustomerById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare(
            "SELECT *, count(stock_id) as nombre FROM " . static::TABLE_2 . " 
            JOIN " . static::TABLE_3 . " s 
            ON s.id=stock_id  WHERE bouquetCustomer_id=:id GROUP BY stock_id"
        );
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function deleteAllFlower(int $bouquetId)
    {
                // prepared request
                $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE_2 . "
                 WHERE bouquetCustomer_id=:bouquet_id");
                $statement->bindValue('bouquet_id', $bouquetId, \PDO::PARAM_INT);
                $statement->execute();
    }

    public function selectLastId()
    {
        // prepared request
        $statement = $this->pdo->query("SELECT MAX(id)  FROM " . static::TABLE);
        return $statement->fetch(\PDO::FETCH_NUM);
    }

    public function selectBouquetCustomer(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE customer_id=:id");
        $statement->bindvalue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * save new item in database.
     */
    public function save(array $bouquetCustomer)
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' VALUES (null, :customer_id, :name)');
        $statement->bindValue('customer_id', $bouquetCustomer['customer_id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $bouquetCustomer['name'], \PDO::PARAM_STR);

        $statement->execute();
    }

    public function saveFlowersInBouquet(array $bouquetCustomer)
    {

        $query = "INSERT INTO " . self::TABLE_2 . " VALUES( 
                " . $bouquetCustomer['stock'] . ", " . $bouquetCustomer['bouquet_id'] . ")";
        $this->pdo->exec($query);
    }

    public function showBouquetCuPanier(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT *
        FROM " . self::TABLE_2 . " sbc  
        JOIN " . self:: TABLE_3 . " s 
        ON s.id=sbc.stock_id WHERE sbc.bouquetCustomer_id=:id ");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }
}
