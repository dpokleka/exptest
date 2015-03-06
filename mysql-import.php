<?php

require 'Utilities/StatsHelper.php';
require 'Utilities/MysqlHelper.php';

use Utilities\StatsHelper;
use Utilities\MysqlHelper;

function usage() {
    echo 'Usage:   mysql-import.php -i CSV_FILE' . PHP_EOL . PHP_EOL;
    echo 'Example: mysql-import.php -i csv/sample.csv' . PHP_EOL;
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

$helper = new StatsHelper(true);

$tableName  = pathinfo($file)['filename'];

StatsHelper::printLine();
echo sprintf('Importing file %s' . PHP_EOL, $file);
StatsHelper::printLine();

try {
    $mh = new MysqlHelper();
    $mh->prepareTable($tableName);

    // invoke mysql import command with proper settings
    $columns = 'user_id,gender,movie_id,rating';
    shell_exec(sprintf(
        "mysqlimport --ignore-lines=1 --fields-terminated-by=, --columns='%s' --local -u %s -p%s %s %s",
        $columns, $mh->getUserName(), $mh->getPassword(), $mh->getDbName(), $file
    ));

    $mh->closeDatabase();

} catch (PDOException $e) {
    die('DB ERROR: ' . $e->getMessage() . PHP_EOL);
}

echo sprintf(
    'Imported file %s (%s) into MYSQL.' . PHP_EOL,
    $file, StatsHelper::human_filesize(filesize($file))
);

$helper->endStats()->printStats();
