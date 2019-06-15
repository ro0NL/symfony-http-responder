ifndef PHP
	PHP=7.3
endif

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	jakzal/phpqa:php${PHP}-alpine

composer_args=--prefer-dist --no-progress --no-interaction --no-suggest
phpunit_args=

# deps
install:
	${qa} composer install ${composer_args}
update:
	${qa} composer update ${composer_args}
update-lowest:
	${qa} composer update ${composer_args} --prefer-stable --prefer-lowest
autoload-dev: install
	${qa} php -r "file_put_contents('var/composer-dev.json', json_encode(array_merge_recursive(json_decode(file_get_contents('composer.json'), true), ['autoload' => ['files' => [glob('/tools/.composer/vendor-bin/symfony/vendor/bin/.phpunit/*')[0].'/vendor/autoload.php']]])));"
	${qa} sh -c "COMPOSER=var/composer-dev.json composer dump-autoload"

# tests
phpunit:
	${qa} simple-phpunit ${phpunit_args}
phpunit-coverage:
	${qa} phpdbg -qrr /tools/simple-phpunit ${phpunit_args} --coverage-clover=var/coverage.xml
phpunit-pull-src:
	rm -rf var/phpunit
	${qa} sh -c "find /tools/.composer/vendor-bin/symfony/vendor/bin/.phpunit/ -maxdepth 1 -type d -name phpunit-\* | head -1 | xargs -I {} cp -R {} var/phpunit"

# code style
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${qa} php-cs-fixer fix

# static analysis
psalm: autoload-dev
	${qa} psalm --show-info=false
psalm-info: autoload-dev
	${qa} psalm --show-info=true

# misc
clean:
	 git clean -dxf var/
smoke-test: clean update phpunit cs psalm
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
