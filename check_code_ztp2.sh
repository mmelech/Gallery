#!/bin/sh
RESULT_FILE="check_code.result.cache"
rm -f -- $RESULT_FILE
touch $RESULT_FILE

echo "Installing dependencies..."
{
  composer install --no-interaction
  composer require --dev friendsofphp/php-cs-fixer  --no-interaction
  composer require --dev squizlabs/php_codesniffer  --no-interaction
  composer require --dev escapestudios/symfony2-coding-standard  --no-interaction
  ./vendor/bin/phpcs --config-set installed_paths $(realpath vendor/escapestudios/symfony2-coding-standard)
  ./vendor/bin/phpcs --config-set default_standard Symfony
} > /dev/null 2>&1
rm -f -- .php-cs-fixer.dist.php
rm -f -- .php-cs-fixer.cache

echo "Running php-cs-fixer..."
./vendor/bin/php-cs-fixer fix src/ --dry-run -vvv --rules=@Symfony,@PSR1,@PSR2,@PSR12 >> $RESULT_FILE
rm -f -- .php-cs-fixer.dist.php
rm -f -- .php-cs-fixer.cache

echo "Running phpcs..."
./vendor/bin/phpcs --standard=Symfony src/ --ignore=Kernel.php >> $RESULT_FILE

echo "Running debug:translation..."
{
  ./bin/console debug:translation en --only-missing
  ./bin/console debug:translation pl --only-missing
} >> $RESULT_FILE

echo "Running DB schema and data fixtures..."
{
  ./bin/console doctrine:schema:drop --no-interaction --full-database --force
  ./bin/console doctrine:migrations:migrate --no-interaction
  ./bin/console doctrine:fixtures:load --no-interaction
}  >> $RESULT_FILE

echo "Running tests..."
rm -f -- .phpunit.result.cache
./bin/phpunit --coverage-text
rm -f -- .phpunit.result.cache

echo "Tear down..."
{
  ./bin/console doctrine:schema:drop --no-interaction --full-database --force
  rm -rf var
  rm -rf vendor
} > /dev/null 2>&1
