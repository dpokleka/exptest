<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage() {
    echo "Usage: mysqlimport.php -i input.csv\n";
    echo "\n Example: mysqlimport.php -i csv/sample.csv\n";
    exit(1);
}

$opts = getopt('i:');
// checks
if (!isset($opts['i'])) {
    usage();
}

$time_start = microtime(true);
$initialMem = memory_get_usage();

$file  = $opts['i'];

if (!file_exists($file)) {
    echo sprintf("File %s does not exist.", $file);
    exit(1);
}

$tableName = pathinfo($file)['filename'];

echo sprintf("Importing file %s\n", $file);

## Connect to a local database server (or die) ##
$dbH = mysqli_connect('localhost', 'root', 'root') or die('Could not connect to MySQL server.' . PHP_EOL . mysqli_error($dbH));

## Select the database to insert to ##
mysqli_select_db($dbH, 'exptest') or die('Could not select database.' . PHP_EOL . mysqli_error($dbH));

$result = mysqli_query($dbH, "SHOW TABLES LIKE '$tableName'");
$tableExists = mysqli_num_rows($result) > 0;

if ($tableExists) {
    mysqli_query($dbH, "TRUNCATE TABLE $tableName;") or
    die('Error truncating table.' . PHP_EOL . mysqli_error($dbH));
} else {
    mysqli_query($dbH, "CREATE TABLE IF NOT EXISTS $tableName (
          user_id int(11) DEFAULT NULL,
          gender set('m','f') DEFAULT NULL,
          movie_id mediumint(9) DEFAULT NULL,
          rating tinyint(4) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ") or die('Error creating table.' . PHP_EOL . mysqli_error($dbH));
}

//mysqli_query($dbH, 'LOAD DATA LOCAL INFILE "' . $file . '" INTO TABLE ' . $tableName . ' FIELDS TERMINATED BY "," LINES TERMINATED BY "\\n";') or
//    die('Error loading data file.' . PHP_EOL . mysqli_error($dbH));

$columns = 'user_id,gender,movie_id,rating';
shell_exec("mysqlimport --ignore-lines=1 --fields-terminated-by=, --columns='$columns' --local -u root -proot exptest $file");

## Close database connection when finished ##
mysqli_close($dbH);

$time_end = microtime(true);
$finalMem = memory_get_peak_usage();
$execution_time = ($time_end - $time_start);

echo sprintf("Imported file %s into MYSQL in %s sec; Memory used: %s \n",
    $file, number_format($execution_time, 2, ',', '.'),
    Helper::human_filesize(filesize($file)), Helper::human_filesize($finalMem - $initialMem)
);
