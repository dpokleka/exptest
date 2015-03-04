<?php

require 'Utilities/Helper.php';
require 'Utilities/MongoHelper.php';

use Utilities\Helper;
use Utilities\MongoHelper;

function usage() {
    echo "Usage: answer1.php -c collection\n";
    echo "\n Example: answer1.php -c sample_150\n";
    exit(1);
}

$opts = getopt('c:');
// checks
if (!isset($opts['c'])) {
    usage();
}

$collectionName = $opts['c'];

$helper = new Helper();
$mh     = new MongoHelper('answer1', $collectionName);

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
    [ '$out' => $mh->outCollectionName ]
];

$cursor = $mh->collection->aggregate($ops);

echo "Collection $mh->outCollectionName created succsessfully\n";

shell_exec("mongoexport -d exptest -c $mh->outCollectionName -f _id,rated_movies --type=csv > $mh->outFile");

echo sprintf("Outputted file %s from MONGO collection %s; Generated file size is: %s; \n",
    $mh->outFile, $mh->outCollectionName, Helper::human_filesize(filesize($mh->outFile))
);

$helper->endStats();
$helper->printStats();