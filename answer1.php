<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage() {
    echo "Usage: answer.php -c collection\n";
    echo "\n Example: answer.php -c sample_150\n";
    exit(1);
}

$opts = getopt('c:');
// checks
if (!isset($opts['c'])) {
    usage();
}

$collectionName = $opts['c'];

$time_start = microtime(true);
$initialMem = memory_get_usage();

echo sprintf("Preparing answer 1 csv for collection %s\n", $collectionName);

$m = new MongoClient();
echo "Connection to database successfully\n";
$db = $m->exptest;
echo "Database exptest selected\n";
$collection = $db->$collectionName;
echo "Collection $collectionName selected succsessfully\n";

$outCollectionName = "out_$collectionName";
$outFile = "answer1/$outCollectionName.csv";
$ops = [
    [
        '$group' => [
            '_id' => '$user_id',
            'rated_movies' => ['$sum' => 1],
        ]
    ],
    [
        '$sort' => [
            '_id' => 1
        ],
    ],
    [ '$out' => $outCollectionName ]
];
$options = array("allowDiskUse" => true);
$cursor = $collection->aggregate($ops, $options);

echo "Collection $outCollectionName created succsessfully\n";

shell_exec("mongoexport -d exptest -c $outCollectionName -f _id,rated_movies --csv > $outFile");


$time_end = microtime(true);
$finalMem = memory_get_peak_usage();
$execution_time = ($time_end - $time_start);

echo sprintf("Outputted file %s from MONGO collection %s in %s sec; Generated file size is: %s; Memory used: %s \n",
    $outFile, $outCollectionName, number_format($execution_time, 2, ',', '.'),
    Helper::human_filesize(filesize($outFile)), Helper::human_filesize($finalMem - $initialMem)
);
