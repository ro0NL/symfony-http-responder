# Contributing

Verify if the CI checks are likely to pass using:

```bash
make smoke-test
```

## Available `make` targets

- autoload-dev
  - install, phpunit-pull-src
- clean
- composer-normalize
  - install
- cs
- cs-fix
- install
- list
- phpunit
- phpunit-coverage
- phpunit-pull-src
- psalm
  - autoload-dev
- psalm-info
  - autoload-dev
- qa-update
- shell
- smoke-test
  - clean, update, phpunit, cs, psalm
- starter-kit-init
- starter-kit-merge
- update
- update-lowest
