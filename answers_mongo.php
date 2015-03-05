<?php

require 'Utilities/Helper.php';
require 'Utilities/MongoHelper.php';

use Utilities\Helper;
use Utilities\MongoHelper;

function usage() {
    echo "Usage: answers_mongo.php.php -a answer_number -c collection\n";
    echo "\n Example: answers_mongo.php.php -a 1 -c sample_150\n";
    exit(1);
}

$opts = getopt('a:c:');
// checks
if (! isset($opts['a']) || ! isset($opts['c'])) {
    usage();
}

$answerNumber   = $opts['a'];
$collectionName = $opts['c'];

$helper = new Helper(true);
$mh     = new MongoHelper($answerNumber, $collectionName);

$mh->execute();

$helper->endStats()->printStats();