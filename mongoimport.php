<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage() {
    echo "Usage: mongoimport.php -i input.csv\n";
    echo "\n Example: mongoimport.php -i csv/sample.csv\n";
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

shell_exec("mongoimport -d exptest -c $tableName --type=csv --file $file --headerline --ignoreBlanks --drop");

$time_end = microtime(true);
$finalMem = memory_get_peak_usage();
$execution_time = ($time_end - $time_start);

echo sprintf("Imported file %s into MONGO in %s sec; Memory used: %s \n",
    $file, number_format($execution_time, 2, ',', '.'),
    Helper::human_filesize(filesize($file)), Helper::human_filesize($finalMem - $initialMem)
);
