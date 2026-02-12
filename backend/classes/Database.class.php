<?php
    // https://github.com/navysingchai/program_documentation
    
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '-1');
    date_default_timezone_set("Asia/Bangkok");
    ini_set('upload_max_filesize', '20M');
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    class Database
    {
        private $host = "";
        private $uname = "";   
        private $passwd = "";
        private $dbname = "";

        public $dbConn;
        public function __construct()
        {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true
            ];
            try {
                $this->dbConn = new PDO($dsn, $this->uname, $this->passwd, $options);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
        public function db()
        {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true
            ];
            try {
                $pdo = new PDO($dsn, $this->uname, $this->passwd, $options);
                return $pdo;
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
?>