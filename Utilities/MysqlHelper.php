<?php

namespace Utilities;
use PDO;
use PDOException;

/**
 * Class MysqlHelper
 *
 * @package   Utilities
 * @author    David Pokleka <david.pokleka@gmail.com>
 */
class MysqlHelper
{
    /**
     * @var string $host
     */
    private $host;
    /**
     * @var string $dbName
     */
    private $dbName;

    /**
     * @var string $userName
     */
    private $userName;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var PDO $db
     */
    private $db;

    /**
     * @param string $host
     * @param string $dbName
     * @param string $userName
     * @param string $password
     */
    public function __construct($host = 'localhost', $dbName = 'exptest', $userName = 'root', $password = 'root')
    {
        $this->dbName   = $dbName;
        $this->host     = $host;
        $this->password = $password;
        $this->userName = $userName;

        $this->db       = $this->setupDatabase();
    }

    /**
     * Create the receiving table if it does not exist and empty it.
     *
     * @param $tableName
     */
    public function prepareTable($tableName)
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS $tableName (
                user_id  int(11)      DEFAULT NULL,
                gender   set('m','f') DEFAULT NULL,
                movie_id mediumint(9) DEFAULT NULL,
                rating   tinyint(4)   DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->db->query("
            TRUNCATE TABLE $tableName;
        ");
    }

    /**
     * Try to connect to DB and create it if it does not exist
     *
     * @return PDO
     */
    private function setupDatabase()
    {
        try {
            $db = new PDO(sprintf('mysql:host=%s;dbname=%s', $this->host, $this->dbName), $this->userName, $this->password);

        } catch (PDOException $e) {

            if (false != strpos($e->getMessage(), 'Unknown database')) {
                $db = new PDO(sprintf('mysql:host=%s', $this->host), $this->userName, $this->password);
                $db->exec("CREATE DATABASE IF NOT EXISTS `$this->dbName` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(print_r($db->errorInfo(), true));

            } else {
                die('DB ERROR: ' . $e->getMessage());
            }

            $db = new PDO(sprintf('mysql:host=%s;dbname=%s', $this->host, $this->dbName), $this->userName, $this->password);
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    /**
     * Close database connection
     */
    public function closeDatabase()
    {
        unset($this->db);
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

}