<?php

namespace App\Model;

class CustomerManager extends AbstractManager
{
    public const TABLE = 'customer';
    public const TABLE_2 = 'command';

    /**
     * Insert new item in database.
     */
    public function insert(array $customer): void
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE .
        ' (`firstname`,`lastname`,`email`,`phone`,`password`) 
        VALUES (:firstname, :lastname, :email, :phone, :password)');
        $statement->bindValue('firstname', $customer['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $customer['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $customer['email'], \PDO::PARAM_STR);
        $statement->bindValue('phone', $customer['phone'], \PDO::PARAM_INT);
        $statement->bindValue('password', $customer['password'], \PDO::PARAM_STR);

        $statement->execute();
    }

    /**
     * Update customer in database.
     */
    public function update(array $customer): bool
    {
        $statement = $this->pdo->prepare('UPDATE ' . self::TABLE .
        ' SET `firstname` = :firstname, `lastname` = :lastname,
         `email` = :email,`phone` = :phone, `password` = :password 
        WHERE id=:id');
        $statement->bindValue('id', $customer['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $customer['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $customer['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $customer['email'], \PDO::PARAM_STR);
        $statement->bindValue('phone', $customer['phone'], \PDO::PARAM_INT);
        $statement->bindValue('password', $customer['password'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    /*
     * get user id associate with email
     */
    public function selectIdByEmail(string $email)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT id FROM " . static::TABLE . " WHERE email=:email");
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    /*
     * get user  associate with email
     */
    public function selectUserByEmail(string $email)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE email=:email");
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectClientCommand($id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM " . static::TABLE . " JOIN " . static::TABLE_2 . " c 
            ON c.customer_id=customer.id  WHERE customer.id=:id "
        );
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
