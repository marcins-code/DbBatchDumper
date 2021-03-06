<?php

include ('archiveHandler.php');

class DataBasesDumper
{

    use archiveHandler;

    /**
     * @var string
     */
    private $dbType;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var string
     */
    private $dns;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $systemDBMySQL = ['information_schema', 'mysql', 'performance_schema', 'sys'];

    /**
     * @var array
     */
    private $systemPostgreSQL = ['postgres'];

    /**
     * @var array
     */
    protected $myDBList;

    /**
     * @var string
     */
    private $subDirPrefix;

    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $dump;


    public function __construct()
    {
        $this->subDirPrefix = '-dump-' .date_create('now')->format('Y-m-d-H-i-s');

    }

    /**
     * Typ bazy danych
     * @param string $dbType
     */
    public function setDbType(string $dbType): void
    {
        $this->dbType = $dbType;
    }

    /**
     * Ścieżka do katalogu archiwum
     * @param string $targetDirectory
     */
    public function setTargetDirectory(string $targetDirectory): void
    {
        $this->targetDirectory = $targetDirectory;

    }


    /**
     * Dane do połaczenia z baza danych
     * @param string $dns
     * @param string $user
     * @param string $password
     */
    public function setConnectionData(string $dns, string $user, string $password): void
    {
        $this->dns = $dns;
        $this->user = $user;
        $this->password = $password;
    }


    /**
     * Połacznie z baza danych
     * @return PDO
     */
    private function getConnection()
    {
        try {
            $this->connection = new PDO($this->dns, $this->user, $this->password);
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->connection;
    }


    /**
     * @return array
     */
    public function getMyDBList():array
    {
        return $this->myDBList;
    }


    /**
     * @param array $myDBList
     */
    public function setMyDBList(array $myDBList): void
    {
        $this->myDBList = $myDBList;
    }


    /**
     * Pobranie wszystkich baz danych
     * @return array
     */
    private function getAllDatabases()
    {
        if ('mysql' === $this->dbType) {
            return $this->getConnection()->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        }


        $sql = "SELECT datname FROM pg_database WHERE datistemplate = false";
        return $this->getConnection()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

    }


    /**
     * Usunięcie z tablicy baz systemowych
     * @return array
     */
    private function getOnlyNonSystemDBs()
    {
        if ('mysql' === $this->dbType) {
            return array_diff($this->getAllDatabases(), $this->systemDBMySQL);
        }

        return array_diff($this->getAllDatabases(), $this->systemPostgreSQL);

    }


    /**
     * Przygotowanie dump
     * @param $array
     * @return string
     */
    private function prepareDump($array): string
    {
        $fullDir = $this->targetDirectory . '/' . $this->dbType . $this->subDirPrefix;
        mkdir($fullDir, 0775);
        $fullDir = str_replace(' ', '\ ', $fullDir);// "escape" dla nazw katalogów ze spacja

        foreach ($array as $sigleDB) {
            if ('mysql' === $this->dbType) {
                $this->dump = exec("(/usr/local/bin/mysqldump -u$this->user -p$this->password $sigleDB | gzip >  $fullDir/$sigleDB.sql.gzip) 2>&1", $output, $exit_status);
            }

            if ('postgresql' === $this->dbType) {
                $this->dump = exec("/usr/local/bin/pg_dump $sigleDB | gzip > $fullDir/$sigleDB.sql.gz");
            }
        }
        return $this->dump;
    }

    /**
     * Metoda zrzuca wszystkie bazy łacznie z sytemowymi
     * @return string
     */
    public function makeDumpWithSystem()
    {
        return $this->prepareDump($this->getAllDatabases());
    }


    /**
     * Metoda zrzuca wszystkie bazy bez systemowych
     * @return string
     */
    public function makeDumpWithoutSystem()
    {
        return $this->prepareDump($this->getOnlyNonSystemDBs());
    }


    public function makeDumpMyDbList()
    {
//        if (! $this->myDBList) {
//            throw new Exception('Empty myDBList array .');
//        }
        return $this->prepareDump($this->getMyDBList());
    }

}

