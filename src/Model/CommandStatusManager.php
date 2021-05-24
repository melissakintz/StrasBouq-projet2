<?php

namespace App\Model;

class CommandStatusManager extends AbstractManager
{
    public const TABLE = "commandStatus";
    public const TABLE_2 = "command";
    public const TABLE_3 = "customer";

    /*
    * insert command status false by default
     */
    public function insertStatus(array $command): void
    {
        $query = ("INSERT INTO " . self::TABLE . "(command_id, isprepared, ispick) 
                    VALUES (:command_id, :isPrepared, :isPick)");
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('command_id', $command['command_id']);
        $statement->bindValue('isPrepared', $command['isPrepared']);
        $statement->bindValue('isPick', $command['isPick']);
        $statement->execute();
    }


    /*
     * select by id
     */
    public function selectOneById(int $commandId)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE command_id=:id");
        $statement->bindValue('id', $commandId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
    /*
    * edit command status
    */
    public function editStatus(int $id, $ispick, $isprepared)
    {
        $query = ("UPDATE " . self::TABLE . " SET isPrepared = :isPrepared, isPick= :isPick WHERE command_id=$id");
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('isPick', $ispick);
        $statement->bindValue('isPrepared', $isprepared);
        $statement->execute();

        header("Location: /Command/showAll");
    }

    public function archiveCommand(string $orderBy = '', string $direction = 'ASC')
    {
        $query = 'SELECT * FROM ' . static::TABLE . ' JOIN ' . static::TABLE_2 . '
         c ON c.id=command_id WHERE ispick = 1 and isprepared = 1';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    public function activeCommand(string $orderBy = '', string $direction = 'DESC')
    {
        $query = 'SELECT * FROM ' . static::TABLE . ' JOIN ' . static::TABLE_2 . ' 
        c ON c.id=command_id JOIN ' . static::TABLE_3 . ' cu ON cu.id=c.customer_id 
        WHERE ispick = 0 or isprepared = 0';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }
}
