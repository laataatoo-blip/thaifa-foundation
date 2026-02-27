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
        private $hosts = [];
        private $uname = "thaifa_fd";
        private $passwd = "*T52Ki6J*jsylalm";
        private $dbname = "thaifa_fd";
        private $port = "3306";

        public $dbConn;

        private function envValue($key, $default = '')
        {
            $v = getenv($key);
            if ($v !== false && $v !== null && $v !== '') return $v;
            if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
            if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
            return $default;
        }

        private function buildHostCandidates()
        {
            $primary = trim((string)$this->envValue('DB_HOST', $this->envValue('MYSQL_HOST', '')));
            $candidates = [];
            if ($primary !== '') $candidates[] = $primary;
            $candidates[] = 'thaifa_db';
            $candidates[] = '127.0.0.1';
            $candidates[] = 'localhost';
            return array_values(array_unique($candidates));
        }

        private function createPdoByHost($host)
        {
            $dsn = "mysql:host={$host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true
            ];
            return new PDO($dsn, $this->uname, $this->passwd, $options);
        }

        private function connect()
        {
            $lastErr = null;
            foreach ($this->hosts as $host) {
                try {
                    return $this->createPdoByHost($host);
                } catch (PDOException $e) {
                    $lastErr = $e;
                }
            }
            if ($lastErr) {
                throw $lastErr;
            }
            throw new PDOException('Database connection failed');
        }

        public function __construct()
        {
            $this->uname = (string)$this->envValue('DB_USER', $this->envValue('MYSQL_USER', $this->uname));
            $this->passwd = (string)$this->envValue('DB_PASS', $this->envValue('MYSQL_PASSWORD', $this->passwd));
            $this->dbname = (string)$this->envValue('DB_NAME', $this->envValue('MYSQL_DATABASE', $this->dbname));
            $this->port = (string)$this->envValue('DB_PORT', $this->envValue('MYSQL_PORT', $this->port));
            $this->hosts = $this->buildHostCandidates();

            try {
                $this->dbConn = $this->connect();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }

        public function db()
        {
            try {
                return $this->connect();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
?>
