<?php

require 'Utilities/StatsHelper.php';
require 'Utilities/MongoHelper.php';

use Utilities\StatsHelper;
use Utilities\MongoHelper;

function usage() {
    echo 'Usage: mongo-answers.php.php -a ANSWER_NUMBER -c COLLECTION_NAME' . PHP_EOL . PHP_EOL;
    echo 'Example: mongo-answers.php.php -a 1 -c sample_150' . PHP_EOL;
    exit(1);
}

$opts = getopt('a:c:');
// checks
if (! isset($opts['a']) || ! isset($opts['c'])) {
    usage();
}

$answerNumber   = $opts['a'];
$collectionName = $opts['c'];

$helper = new StatsHelper(true);
$mh     = new MongoHelper($answerNumber, $collectionName);

$mh->execute();

$helper->endStats()->printStats();