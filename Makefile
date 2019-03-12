ifndef PHP
	PHP=7.2
endif
ifndef PHPUNIT
	PHPUNIT=7.5
endif

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_HOME=/app/var/composer \
	-e SYMFONY_PHPUNIT_DIR=/app/var/phpunit \
	-e SYMFONY_PHPUNIT_VERSION=${PHPUNIT} \
	jakzal/phpqa:php${PHP}-alpine
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

# deps
install:
	${qa} composer install ${composer_args}
update:
	${qa} composer update ${composer_args}
update-lowest:
	${qa} composer update ${composer_args} --prefer-stable --prefer-lowest

# tests
phpunit-install:
	${qa} simple-phpunit install
phpunit:
	${qa} simple-phpunit
phpunit-coverage:
	${qa} phpdbg -qrr /tools/.composer/vendor/bin/simple-phpunit --coverage-clover=var/coverage.xml

# code style / static analysis
cs:
	mkdir -p var
	${qa} php-cs-fixer fix --dry-run --verbose --diff --config=.php_cs src/ tests/
cs-fix:
	mkdir -p var
	${qa} php-cs-fixer fix --config=.php_cs src/ tests/
sa: install phpunit-install
	${qa} phpstan analyse
	${qa} psalm --show-info=false

# misc
clean:
	rm -rf var/phpstan var/psalm var/php-cs-fixer.cache
smoke-test: clean update phpunit cs sa
shell:
	${qa} /bin/sh
composer-normalize: install
	${qa} composer global require ${composer_args} localheinz/composer-normalize
	${qa} composer normalize

# starter-kit
starter-kit-init:
	git remote add --fetch starter-kit git@github.com:ro0NL/php-package-starter-kit.git
starter-kit-merge:
	git merge --no-commit --no-ff --allow-unrelated-histories starter-kit/master
