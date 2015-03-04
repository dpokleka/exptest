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

$time_start = microtime(true);
$initialMem = memory_get_usage();

$collectionName = $opts['c'];
echo sprintf("Preparing answer 1 csv for collection %s\n", $collectionName);

MongoCursor::$timeout = -1;
$m = new MongoClient();
echo "Connection to database successfully\n";

$collection = $m->selectDB('exptest')->selectCollection($collectionName);
echo "Database exptest with collection $collectionName selected\n";

$outCollectionName = "out_$collectionName";
$outFile = "answer1/$outCollectionName.csv";
$ops = [
    [
        '$group' => [
            '_id' => [
                'user_id'  => '$user_id',
                'movie_id' => '$movie_id'
            ]
        ]
    ],
    [
        '$group' => [
            '_id'   => '$_id.user_id',
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

$cursor = $collection->aggregate($ops);

echo "Collection $outCollectionName created succsessfully\n";

shell_exec("mongoexport -d exptest -c $outCollectionName -f _id,rated_movies --type=csv > $outFile");

$time_end = microtime(true);
$finalMem = memory_get_peak_usage();
$execution_time = ($time_end - $time_start);

echo sprintf("Outputted file %s from MONGO collection %s in %s sec; Generated file size is: %s; Memory used: %s \n",
    $outFile, $outCollectionName, number_format($execution_time, 2, ',', '.'),
    Helper::human_filesize(filesize($outFile)), Helper::human_filesize($finalMem - $initialMem)
);
