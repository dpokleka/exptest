<?php

namespace Utilities;

/**
 * Class StatsHelper
 *
 * @package   Utilities
 * @author    David Pokleka <david.pokleka@gmail.com>
 */
class StatsHelper {

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


    /**
     * @param bool $autoStartStats
     */
    public function __construct($autoStartStats = true)
    {
        if ($autoStartStats) {
            $this->startStats();
        }
    }

    /**
     * Start the stats measurement
     *
     * @return $this
     */
    public function startStats()
    {
        $this->timeStart     = microtime(true);
        $this->initialMemory = memory_get_usage();

        return $this;
    }

    /**
     * Stops the stats measurement
     *
     * @return $this
     */
    public function endStats()
    {
        $this->timeEnd     = microtime(true);
        $this->finalMemory = memory_get_peak_usage();

        return $this;
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
        self::printLine();
        echo sprintf(
            'Script duration %s s; Memory peak consumption: %s' . PHP_EOL,
             number_format($this->getExecuionTime(), 2, ',', '.'), self::human_filesize($this->getMemoryConsuption())
        );
        self::printLine();
    }

    public static function printLine()
    {
        echo '------------------------------------------------------------' . PHP_EOL;
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