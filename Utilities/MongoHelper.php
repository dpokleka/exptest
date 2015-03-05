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
     * @var int $answerNumber
     */
    public $answerNumber;

    /**
     * @var string $answerDir
     */
    public $answerDir;

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
     * @param $answerNumber   int
     * @param $collectionName string
     */
    public function __construct($answerNumber, $collectionName)
    {
        $this->answerNumber     = $answerNumber;
        $this->answerDir        = sprintf('answer%s', $answerNumber);
        $this->collectionName   = $collectionName;
        $this->dbName           = 'exptest';

        Helper::printLine();
        echo sprintf('Preparing %s csv file for collection %s' . PHP_EOL, $this->answerDir, $this->collectionName);

        MongoCursor::$timeout = -1;
        $mc = new MongoClient();
        echo "Connection to database successfully" . PHP_EOL;

        $this->collection = $mc->selectDB($this->dbName)->selectCollection($this->collectionName);
        echo sprintf('Database %s with collection %s selected' . PHP_EOL,
                     $this->dbName, $this->collectionName
        );

        if (! file_exists($this->answerDir)) {
            mkdir($this->answerDir, 0775, true);
        }

        $this->outCollectionName = sprintf('out_%s_%s', $answerNumber, $collectionName);
        $this->outFile           = sprintf('%s/%s.csv', $this->answerDir, $this->outCollectionName);
    }


    public function execute()
    {
        if (null === ($answerSettings = $this->getAnswerSettings($this->answerNumber))) {

            return false;
        }

        $cursor = $this->collection->aggregate($answerSettings['operators']);

        if (! $cursor['ok']) {
            echo sprintf("Collection %s NOT created successfully" . PHP_EOL, $this->outCollectionName);
            echo sprintf("Error: %s; code:%s" . PHP_EOL . PHP_EOL, $cursor['errmsg'], $cursor['code']);

            return false;
        }

        echo sprintf("Collection %s created successfully" . PHP_EOL, $this->outCollectionName);

        $command = sprintf(
            "mongoexport -d exptest -c %s -f %s --type=csv > %s",
            $this->outCollectionName, $answerSettings['field_list'], $this->outFile
        );

        shell_exec($command);

        echo sprintf(
            "Answer %s collection outputted into %s; Generated file size is: %s; \n",
            $this->answerNumber, $this->outFile, Helper::human_filesize(filesize($this->outFile))
        );
    }

    /**
     * @param $answer
     * @return array|null
     */
    private function getAnswerSettings($answer)
    {
        $answerSettings = [
            '1' => [
                'operators' => [
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
                    [
                        '$out' => $this->outCollectionName
                    ]
                ],
                'field_list' => '_id,rated_movies'
            ],
            '2' => [
                'operators' => [
                    [
                        '$group' => [
                            '_id'       => '$gender',
                            'all_rates' => ['$sum' => 1]
                        ]
                    ],
                    [
                        '$sort' => [
                            '_id' => 1
                        ],
                    ],
                    [
                        '$out' => $this->outCollectionName
                    ],
                ],
                'field_list' => '_id,all_rates'
            ],
            '3' => [
                'operators' => [
                    [
                        '$group' => [
                            '_id' => [
                                'user_id'  => '$user_id',
                                'gender'   => '$gender',
                                'movie_id' => '$movie_id'
                            ]
                        ]
                    ],
                    [
                        '$group' => [
                            '_id'   => '$_id.gender',
                            'watched_movies' => ['$sum' => 1],
                        ]
                    ],
                    [
                        '$sort' => [
                            '_id' => 1
                        ],
                    ],
                    [
                        '$out' => $this->outCollectionName
                    ]
                ],
                'field_list' => '_id,watched_movies'
            ],
            '4' => [],
        ];

        return (array_key_exists($answer, $answerSettings) ? $answerSettings[$answer] : null);
    }
}
