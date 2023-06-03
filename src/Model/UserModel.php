<?php
namespace Src\Model;

class UserModel {
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function insert(Array $input)
    {
        $statement = "
                INSERT INTO user
                    (firstname, lastname, email)
                VALUES
                    (:firstname, :lastname, :email);
            ";

        try {
          $statement = $this->db->prepare($statement);
          $statement->execute(array(
            'firstname' => $input['firstname'],
            'lastname'  => $input['lastname'],
            'email' => $input['email']
          ));
          return $statement->rowCount();
        } catch (\PDOException $e) {
          exit($e->getMessage());
        }
    }

    public function findAll()
    {
        $statement = "
            SELECT
                id, firstname, lastname, email
            FROM
                user;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT
                id, firstname, lastname, email
            FROM
                user
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $statement = "
                DELETE FROM user
                WHERE id = :id;
            ";

        try {
          $statement = $this->db->prepare($statement);
          $statement->execute(array('id' => $id));
          return $statement->rowCount();
        } catch (\PDOException $e) {
          exit($e->getMessage());
        }
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE user
            SET
                firstname = :firstname,
                lastname  = :lastname,
                email = :email
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'firstname' => $input['firstname'],
                'lastname'  => $input['lastname'],
                'email' => $input['email']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}