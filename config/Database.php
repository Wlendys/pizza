<?php

/**
 * class Database
 */
class Database
{
    private $host = 'localhost';
    private $db_name = 'pizza_api';
    private $username = 'root';
    private $password = '';
    public $dbh;

    /**
     * Database connect
     */
    public function __construct()
    {
        $this->dbh = NULL;
        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connect error:' . $e->getMessage();
        }
        return $this->dbh;
    }

    /**
     * Check Is PDO Object
     */
    public function checkPdo()
    {
        if ($this->dbh instanceof PDO) {
            return $this->dbh;
        }
    }
}
