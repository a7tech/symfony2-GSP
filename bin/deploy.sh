#! /bin/bash

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

php app/console user:roles:import

php app/console doctrine:fixtures:load --fixtures=src/App/LanguageBundle/DataFixtures/ORM --append
php app/console doctrine:fixtures:load --fixtures=src/App/AddressBundle/DataFixtures/ORM --append
php app/console doctrine:fixtures:load --fixtures=src/App/PhoneBundle/DataFixtures/ORM --append
php app/console doctrine:fixtures:load --fixtures=src/App/CalendarBundle/DataFixtures/ORM --append

mysql -u root -p07101985 gsp < bin/gsp-core.sql
mysql -u root -p07101985 gsp < bin/gsp-data.sql

php app/console doctrine:fixtures:load --fixtures=src/App/UserBundle/DataFixtures/ORM --append