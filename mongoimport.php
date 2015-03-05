<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage() {
    echo 'Usage:   mongoimport.php -i input.csv' . PHP_EOL . PHP_EOL;
    echo 'Example: mongoimport.php -i csv/sample.csv' . PHP_EOL;
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

$tableName = pathinfo($file)['filename'];

Helper::printLine();
echo sprintf('Importing file %s' . PHP_EOL, $file);
Helper::printLine();

shell_exec("mongoimport -d exptest -c $tableName --type=csv --file $file --headerline --ignoreBlanks --drop");

echo sprintf(
    'Imported file %s (%s) into MONGO' . PHP_EOL,
    $file, Helper::human_filesize(filesize($file))
);

$helper->endStats()->printStats();
