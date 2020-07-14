#!/bin/sh

php artisan migrate:fresh
php artisan db:seed

./vendor/bin/phpunit --configuration phpunit_test_db.xml
