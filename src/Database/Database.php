<?php
namespace Src\Database;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private $db;

    private string $host = 'localhost:3306';
    private string $user = 'root';
    private string $pass = '';
    private string $database = 'tec_psd2';

    private function __construct(){
        try {
            $this->db = new PDO("mysql:host={$this->host};dbname={$this->database}",
                $this->user,
                $this->pass
            );
        }catch (PDOException $e){
            // to do
            die();
        }
    }

    public static function getInstance(){
        if (!self::$instance){
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->db;
    }
}
