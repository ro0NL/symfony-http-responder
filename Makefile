ifndef PHP
	PHP=7.3
endif
ifndef PHPUNIT
	PHPUNIT=8.2
endif

qa_image=jakzal/phpqa:php${PHP}-alpine
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest
phpunit_args=

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	-e SYMFONY_PHPUNIT_VERSION=${PHPUNIT} \
	${qa_image}

# deps
install:
	${qa} composer install ${composer_args}
update:
	${qa} composer update ${composer_args}
update-lowest:
	${qa} composer update ${composer_args} --prefer-stable --prefer-lowest

# tests
phpunit:
	${qa} simple-phpunit ${phpunit_args}
phpunit-coverage:
	${qa} phpdbg -qrr /tools/simple-phpunit ${phpunit_args} --coverage-clover=var/coverage.xml
phpunit-pull:
	rm -rf var/phpunit
	${qa} sh -c "cp -RL /tools/.composer/vendor-bin/symfony/vendor/bin/.phpunit/phpunit-${PHPUNIT} var/phpunit"

# code style
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${qa} php-cs-fixer fix

# static analysis
psalm: install phpunit-pull
	${qa} psalm --show-info=false
psalm-info: install phpunit-pull
	${qa} psalm --show-info=true

# starter-kit
starter-kit-init:
	git remote add starter-kit git@github.com:ro0NL/php-package-starter-kit.git
starter-kit-merge:
	git fetch starter-kit master
	git merge --no-commit --no-ff --allow-unrelated-histories starter-kit/master

# phpqa
qa-update:
	docker rmi -f ${qa_image}
	docker pull ${qa_image}

# misc
clean:
	 git clean -dxf var/
smoke-test: clean update phpunit cs psalm
shell:
	${qa} /bin/sh
composer-normalize: install
	${qa} composer normalize
