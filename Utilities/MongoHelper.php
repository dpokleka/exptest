<?php

namespace Utilities;
use MongoClient;
use MongoCollection;
use MongoCursor;

/**
 * Class MongoHelper
 *
 * @package   Utilities
 * @author    David Pokleka <david.pokleka@gmail.com>
 */
class MongoHelper
{

    /**
     * @var string $collectionName
     */
    public $collectionName;

    /**
     * @var string $answer
     */
    public $answer;

    /**
     * @var MongoCollection $collection
     */
    public $collection;

    /**
     * @var string $outFile
     */
    public $outFile;

    /**
     * @var string $outCollectionName
     */
    public $outCollectionName;

    /**
     * @param $answer         string
     * @param $collectionName string
     */
    public function __construct($answer, $collectionName)
    {
        $this->answer       = $answer;
        $this->collectionName   = $collectionName;

        echo sprintf("Preparing %s csv for collection %s\n", $answer, $collectionName);

        MongoCursor::$timeout = -1;
        $m = new MongoClient();
        echo "Connection to database successfully\n";

        $$this->collection = $m->selectDB('exptest')->selectCollection($collectionName);
        echo "Database exptest with collection $collectionName selected\n";

        $this->outCollectionName = sprintf('out_%s', $collectionName);
        $this->outFile           = sprintf('%s/%s.csv', $answer, $this->outCollectionName);
    }
}