Please make sure to update `composer`. Commands below are tested and running with `composer -V`

    Composer version 2.4.1 2022-08-20 11:44:50

STEPS TO INSTALL

Install dependencies via composer:

    composer install

Setup database environment from docker:

    docker-compose up -d

Run migration:

    symfony console doctrine:migrations:migrate

Setup TEST db for unit test:

    symfony console doctrine:database:create --env=test
    symfony console doctrine:schema:create --env=test

And the DATABASE should be READY at this point...

***
Run the IMPORT COMMAND by default (to import 100 records with AU nationaliy)

    symfony console app:import-data-random-user-api

OR to import just 10 records:
***

After running the import command above, our Customer API should be ready.

We start the server `symfony server:start`

And we can access API directly from browser or using POSTMAN:

    http://localhost:8000/customers

And to fetch customer record:

    http://localhost:8000/customers/1
***
To run test, please run the code below:

    symfony php bin/phpunit tests/StockTest.php

    symfony console app:import-data-random-user-api 10
    