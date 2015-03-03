<?php

require 'Utilities/Helper.php';

use Utilities\Helper;

function usage()
{
    echo "Usage: generate-csv.php -n NUMBER_OF_CSV_ROWS -o DIR\n";
    echo "\n Example: generate-csv.php -n 1000 -o /tmp\n";
    exit(1);
}

$opts = getopt('n:o:');
// checks
if (!isset($opts['n'])) {
    usage();
}
if (!isset($opts['o'])) {
    $dir = '/tmp';
} else {
    $dir = $opts['o'];
}

$time_start = microtime(true);
$initialMem = memory_get_usage();

$lines = $opts['n'];
$file  = "$dir/sample_$lines.csv";

echo sprintf("Outputting %s lines into %s\n", number_format($lines,0,',', '.'), $file);

if (! file_exists(dirname($file))) {
    mkdir(dirname($file), 0775, true);
}
$handle = fopen($file, 'w');

fputcsv($handle, array('user_id', 'gender', 'movie_id', 'rating'));

$maxUsers  = (int)(sqrt($lines) * log($lines, 2));
$maxMovies = (int)sqrt($lines);
echo sprintf("Max users: %s, Max movies: %s \n",
    number_format($maxUsers, 0, ',', '.'), number_format($maxMovies, 0, ',', '.')
);

$movieRatesForUserAssigned = 0;
$currentUserId = 1;
$i = 1;
$percentLines = (int)$lines/100;
echo "Progress :      ";  // 5 characters of padding at the end
while ($i <= $lines) {

    if ($movieRatesForUserAssigned == 0) {
        $movieRatesPerUser = rand((int) log($lines, 2), 4 * (int) log($lines, 2));
    }

    $userId     = $currentUserId;
    $gender     = $i % 2 === 1 ? 'm' : 'f';
    $movieId    = rand(1, $maxMovies);
    $rating     = (int) (rand(20, 95) * rand(95, 105) / 100);

    fputcsv($handle, array($userId, $gender, $movieId, $rating));
    $i++;
    $movieRatesForUserAssigned++;

    if ($movieRatesForUserAssigned == $movieRatesPerUser ) {
        $movieRatesForUserAssigned = 0;
        $currentUserId++;
    }

    if ($i % $percentLines === 0) {
        echo "\033[5D";      // Move 5 characters backward
        echo str_pad((int)($i/$percentLines), 3, ' ', STR_PAD_LEFT) . " %";    // Output is always 5 characters long
    }
}

fclose($handle);
echo "\n";

$time_end = microtime(true);
$finalMem = memory_get_peak_usage();
$execution_time = ($time_end - $time_start);

echo sprintf("Finished outputting %s lines in %s sec; Generated file size is: %s; Memory used: %s \n",
    number_format($lines,0,',', '.'), number_format($execution_time, 2, ',', '.'),
    Helper::human_filesize(filesize($file)), Helper::human_filesize($finalMem - $initialMem)
);
