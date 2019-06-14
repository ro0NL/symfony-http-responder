ifndef PHP
	PHP=7.3
endif
ifndef PHPUNIT
	PHPUNIT=8.2
endif

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	-e SYMFONY_PHPUNIT_DIR=/app/var/phpunit \
	-e SYMFONY_PHPUNIT_VERSION=${PHPUNIT} \
	jakzal/phpqa:php${PHP}-alpine

composer_args=--prefer-dist --no-progress --no-interaction --no-suggest
phpunit_args=--do-not-cache-result

# deps
install: phpunit-install
	${qa} composer install ${composer_args}
update: phpunit-install
	${qa} composer update ${composer_args}
update-lowest: phpunit-install
	${qa} composer update ${composer_args} --prefer-stable --prefer-lowest

# tests
phpunit-install:
	${qa} simple-phpunit install
phpunit:
	${qa} simple-phpunit ${phpunit_args}
phpunit-coverage:
	${qa} phpdbg -qrr /tools/simple-phpunit ${phpunit_args} --coverage-clover=var/coverage.xml

# code style
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${qa} php-cs-fixer fix

# static analysis
phpstan: install
	${qa} phpstan analyse
psalm: install
	${qa} psalm --show-info=false
psalm-info: install
	${qa} psalm --show-info=false

# misc
clean:
	 git clean -dxf var/
smoke-test: clean update phpunit cs phpstan psalm
shell:
	${qa} /bin/sh
composer-normalize: install
	${qa} composer normalize

# starter-kit
starter-kit-init:
	git remote add starter-kit git@github.com:ro0NL/php-package-starter-kit.git
starter-kit-merge:
	git fetch starter-kit master
	git merge --no-commit --no-ff --allow-unrelated-histories starter-kit/master
