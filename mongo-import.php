<?php

require 'Utilities/StatsHelper.php';

use Utilities\StatsHelper;

function usage() {
    echo 'Usage:   mongo-import.php -i CSV_FILE' . PHP_EOL . PHP_EOL;
    echo 'Example: mongo-import.php -i csv/sample.csv' . PHP_EOL;
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

$tableName = pathinfo($file)['filename'];

StatsHelper::printLine();
echo sprintf('Importing file %s' . PHP_EOL, $file);
StatsHelper::printLine();

shell_exec("mongoimport -d exptest -c $tableName --type=csv --file $file --headerline --ignoreBlanks --drop");

echo sprintf(
    'Imported file %s (%s) into MONGO' . PHP_EOL,
    $file, StatsHelper::human_filesize(filesize($file))
);

$helper->endStats()->printStats();
