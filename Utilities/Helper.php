<?php

namespace Utilities;

/**
 * Class Helper
 *
 * @package   Utilities
 */
class Helper {

    /**
     * @var float $timeStart
     */
    private $timeStart;

    /**
     * @var int $initialMemory
     */
    private $initialMemory;

    /**
     * @var float $timeEnd
     */
    private $timeEnd;

    /**
     * @var int $finalMemory
     */
    private $finalMemory;


    function __construct()
    {
        $this->startStats();
    }


    /**
     * Start the stats measurement
     */
    public function startStats() {
        $this->timeStart     = microtime(true);
        $this->initialMemory = memory_get_usage();
    }

    /**
     * Stops the stats measurement
     */
    public function endStats() {
        $this->timeEnd      = microtime(true);
        $this->finalMemory = memory_get_peak_usage();
    }

    /**
     * @return float
     */
    public function getExecuionTime()
    {
        return $this->timeEnd - $this->timeStart;
    }

    /**
     * @return mixed
     */
    public function getMemoryConsuption()
    {
        return $this->finalMemory - $this->initialMemory;
    }

    public function printStats()
    {
        echo sprintf("Script duration %s s; Memory peak consumption: %s\n",
                     number_format($this->getExecuionTime(), 2, ',', '.'),
                     self::human_filesize($this->getMemoryConsuption())
             );
    }

    /**
     * @param int $bytes
     * @param int $dec
     *
     * @return string
     */
    public static function human_filesize($bytes, $dec = 2)
    {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

}