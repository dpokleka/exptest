<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage() {
    echo 'Usage:   mysqlimport.php -i input.csv' . PHP_EOL . PHP_EOL;
    echo 'Example: mysqlimport.php -i csv/sample.csv' . PHP_EOL;
    exit(1);
}

$opts = getopt('i:');
// checks
if (!isset($opts['i'])) {
    usage();
}
$file  = $opts['i'];

if (!file_exists($file)) {
    echo sprintf('File %s does not exist.' . PHP_EOL, $file);
    exit(1);
}

$helper = new Helper(true);

$host       = 'localhost';
$dbName     = 'exptest2';
$userName   = 'root';
$password   = 'root';
$tableName  = pathinfo($file)['filename'];

Helper::printLine();
echo sprintf('Importing file %s' . PHP_EOL, $file);
Helper::printLine();

try {
    $db = new PDO(sprintf('mysql:host=%s;dbname=%s', $host, $dbName), $userName, $password);

} catch (PDOException $e) {

    if (strpos($e->getMessage(), 'Unknown database') !== false ) {
        $db = new PDO(sprintf('mysql:host=%s', $host), $userName, $password);
        $db->exec("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(print_r($dbh->errorInfo(), true));
    } else {
        die('DB ERROR: ' . $e->getMessage());
    }
    $db = new PDO(sprintf('mysql:host=%s;dbname=%s', $host, $dbName), $userName, $password);
}

try {
    $db->query("
        CREATE TABLE IF NOT EXISTS $tableName (
            user_id  int(11)      DEFAULT NULL,
            gender   set('m','f') DEFAULT NULL,
            movie_id mediumint(9) DEFAULT NULL,
            rating   tinyint(4)   DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
    $db->query("
        TRUNCATE TABLE $tableName;
    ");

    $columns = 'user_id,gender,movie_id,rating';
    shell_exec("mysqlimport --ignore-lines=1 --fields-terminated-by=, --columns='$columns' --local -u $userName -p$password $dbName $file");

    ## Close database connection when finished ##
    unset($db);

} catch (PDOException $e) {
    die('DB ERROR: ' . $e->getMessage() . PHP_EOL);
}

echo sprintf(
    'Imported file %s (%s) into MYSQL.' . PHP_EOL,
    $file, Helper::human_filesize(filesize($file))
);

$helper->endStats()->printStats();
