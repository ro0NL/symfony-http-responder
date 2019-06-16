ifndef PHP
	PHP=7.3
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
	${qa_image}

# deps
install:
	${qa} composer install ${composer_args}
update:
	${qa} composer update ${composer_args}
update-lowest:
	${qa} composer update ${composer_args} --prefer-stable --prefer-lowest
autoload-dev: install phpunit-pull-src
	${qa} php -r "file_put_contents('var/composer-dev.json', json_encode(array_merge_recursive(json_decode(file_get_contents('composer.json'), true), ['autoload' => ['files' => ['var/phpunit/vendor/autoload.php']]])));"
	${qa} sh -c "COMPOSER=var/composer-dev.json composer dump-autoload"

# tests
phpunit:
	${qa} simple-phpunit ${phpunit_args}
phpunit-coverage:
	${qa} phpdbg -qrr /tools/simple-phpunit ${phpunit_args} --coverage-clover=var/coverage.xml
phpunit-pull-src:
	rm -rf var/phpunit
	${qa} sh -c "find /tools/.composer/vendor-bin/symfony/vendor/bin/.phpunit/ -maxdepth 1 -type d -name phpunit-\* | head -1 | xargs -I {} cp -RL {} var/phpunit"

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
list:
	#make -p | sort | grep -E "^([a-z]++\-?)++\:(\ ([a-z]++\-?)++)+*$"
	echo "$$(make -p | sort | grep -E '^([a-z]++\-?)++\:')" | while read -r cmd; do echo "- $${cmd%:*}"; if [ -n "$${cmd#*:}" ]; then echo -n "  - "; echo "$${cmd#*:}" | cut -c2- | sed -e "s/ /, /g"; fi; done
clean:
	 git clean -dxf var/
smoke-test: clean update phpunit cs psalm
shell:
	${qa} /bin/sh
composer-normalize: install
	${qa} composer normalize
