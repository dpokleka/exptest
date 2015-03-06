# Backend Software Engineer Expertise Test

## Movie rating exercise

This repo holds the code that accompanies the exercise solution 
found in this [google document](https://docs.google.com/document/d/1YCfQoDRuZINT1H8cbPVfFLQDSjQGF-tx_p4X1zkeeuc):


### generate-csv.php

This script is used to generate csv files of desired size with the structure specified in the exercise.

> **Usage:**   `generate-csv.php -n NUMBER_OF_CSV_ROWS -o DIR`

> **Example:** `generate-csv.php -n 1000 -o csv`


### mysqlimport.php

This script is used to import the generated csv files into a **MySql** database
 The resulting table name will be the same as the csv file used for importing.
 
> **Usage:**   `mysql-import.php -i CSV_FILE`

> **Example:** `mysql-import.php -i csv/sample_1000.csv`


### mongoimport.php

This script is used to import the generated csv files into a **Mongo** database.
The resulting collection name will be the same as the csv file used for importing.

> **Usage:**   `mongo-import.php -i CSV_FILE`

> **Example:** `mongo-import.php -i csv/sample_1000.csv`


### answers_mongo.php

This script is used to generate the results from sample data stored in the **mongo database** helping answer the 4 questions in the exercise.

> **Usage:**   `answers_mongo.php.php -a ANSWER_NUMBER -c COLLECTION_NAME`

> **Example:** `answers_mongo.php.php -a 1 -c sample_1000`
