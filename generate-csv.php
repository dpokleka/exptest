<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage()
{
    echo 'Usage:   generate-csv.php -n NUMBER_OF_CSV_ROWS -o DIR' . PHP_EOL . PHP_EOL;
    echo 'Example: generate-csv.php -n 1000 -o csv' . PHP_EOL;
    exit(1);
}

$opts = getopt('n:o:');
// checks
if (!isset($opts['n'])) {
    usage();
}
if (!isset($opts['o'])) {
    $dir = 'csv';
} else {
    $dir = $opts['o'];
}

$lines = $opts['n'];

$file  = sprintf('%s/sample_%s.csv', $dir, $lines) ;
if (! file_exists(dirname($file))) {
    mkdir(dirname($file), 0775, true);
}

$helper = new Helper(true);

Helper::printLine();
echo sprintf('Outputting %s lines into %s' . PHP_EOL, number_format($lines,0,',', '.'), $file);
Helper::printLine();

$handle = fopen($file, 'w');
fputcsv($handle, array('user_id', 'gender', 'movie_id', 'rating'));

$maxUsers  = (int)(sqrt($lines) * log($lines, 2));
$maxMovies = (int)sqrt($lines);
echo sprintf('Max users: %s, Max movies: %s ' . PHP_EOL,
    number_format($maxUsers, 0, ',', '.'), number_format($maxMovies, 0, ',', '.')
);

$movieRatesForUserAssigned  = 0;
$movieRatesPerUser          = 5;
$currentUserId              = 1;
$currentGender              = 'm';
$i                          = 1;
$percentLines               = (int)$lines/100;
echo 'Progress :      ';  // 5 characters of padding at the end

while ($i <= $lines) {

    if ($movieRatesForUserAssigned == 0) {
        $movieRatesPerUser = rand((int) log($lines, 2), 4 * (int) log($lines, 2));
    }

    $userId     = $currentUserId;
    $gender     = $currentGender;
    $movieId    = rand(1, $maxMovies);
    $rating     = (int) (rand(20, 95) * rand(95, 105) / 100);

    fputcsv($handle, array($userId, $gender, $movieId, $rating));
    $i++;
    $movieRatesForUserAssigned++;

    if ($movieRatesForUserAssigned == $movieRatesPerUser ) {
        $movieRatesForUserAssigned = 0;
        $currentUserId++;
        $currentGender = ord(md5($currentUserId)) % 2 === 1 ? 'm' : 'f';
    }

    if ($i % $percentLines === 0) {
        echo "\033[5D";      // Move 5 characters backward
        echo str_pad((int)($i/$percentLines), 3, ' ', STR_PAD_LEFT) . " %";    // Output is always 5 characters long
    }
}

fclose($handle);

echo sprintf(
    PHP_EOL . 'Finished outputting %s lines; Generated file size is: %s ' . PHP_EOL,
    number_format($lines, 0, ',', '.'), Helper::human_filesize(filesize($file))
);

$helper->endStats()->printStats();
