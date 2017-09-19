#
# Makefile
#
up: install init

# Install
install: update-bin install-composer

# Binaries
update-bin: bin/composer
	./bin/composer self-update
	
bin/composer:
	curl -sS https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer
	chmod +x bin/composer || /bin/true

install-composer:
	./bin/composer install --no-interaction -o

init:
	cp app/config/parameters.yml.dist app/config/parameters.yml
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:create
	php bin/console doctrine:fixtures:load -n
	php bin/console server:start

# Tests
test:
	./vendor/bin/phpunit
test-coverage:
	./vendor/bin/phpunit --coverage-html var/coverage
