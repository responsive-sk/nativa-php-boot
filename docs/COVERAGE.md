# Code Coverage with C3

This project uses [C3](https://github.com/Codeception/c3) for server-side code coverage reporting.

## Setup

### Requirements

- PHP 8.4+
- Xdebug (optional, for local coverage)
- Codeception C3

### Installation

C3 is already included in dev dependencies:

```bash
composer install
```

## Running Coverage Reports

### With PHPUnit (Local Coverage)

```bash
# Requires Xdebug
composer test-coverage

# View report
open tests/_output/coverage/index.html
```

### With C3 (Server Coverage)

```bash
# Start server
bin/cms serve

# Run C3 coverage tests
composer test-c3

# View report
open tests/_output/coverage/index.html
```

### Acceptance Tests Coverage (NEW!)

Acceptance tests provide real browser testing with server-side coverage:

```bash
# Start server in terminal 1
bin/cms serve

# Run acceptance tests in terminal 2
vendor/bin/codecept run Acceptance

# View combined coverage
open tests/_output/coverage/index.html
```

## How C3 Works

1. C3 intercepts code coverage data from server requests
2. Coverage data is stored in `tests/_output/c3`
3. After tests run, coverage is merged and reported

## Configuration

### phpunit.xml

```xml
<phpunit>
    <coverage>
        <include>
            <directory>domain</directory>
            <directory>application</directory>
            <directory>infrastructure</directory>
            <directory>interfaces</directory>
        </include>
    </coverage>
</phpunit>
```

### codeception.yml

```yaml
coverage:
    enabled: true
    include:
        - domain/*
        - application/*
        - infrastructure/*
        - interfaces/*
    c3_url: http://localhost:8000/c3/report
```

## Coverage Goals

| Layer | Goal | Current | Tests |
|-------|------|---------|-------|
| Domain | 90% | ✅ ~85% | Unit tests |
| Application | 80% | ✅ ~75% | Unit tests |
| Infrastructure | 70% | 🔄 ~60% | Integration tests |
| Interfaces | 50% | 🔄 ~40% | Acceptance tests |
| Acceptance | 100% | ✅ 100% | Login, Dashboard, Roles, Permissions |

## Troubleshooting

### C3 not collecting coverage

1. Ensure `c3.php` is included in `public/index.php`
2. Check C3 URL in `codeception.yml` matches your server
3. Verify `tests/_output` is writable
4. For acceptance tests, ensure server is running on correct port

### Acceptance tests failing

1. Ensure server is running: `bin/cms serve`
2. Check database is seeded: `bin/cms seed`
3. Verify test credentials match seeded data
4. Clear test output: `rm -rf tests/_output/*`

### Xdebug not loaded

For local coverage reports, install Xdebug:

```bash
# Ubuntu/Debian
pecl install xdebug
echo "zend_extension=xdebug.so" >> php.ini

# Verify
php -v | grep Xdebug
```

## Reports

Coverage reports are generated in:
- HTML: `tests/_output/coverage/index.html`
- PHP: `tests/_output/coverage.cov`
- Text: `tests/_output/coverage.txt`

## CI/CD Integration

```yaml
# GitHub Actions example
- name: Run coverage
  run: composer test-coverage

- name: Upload coverage
  uses: codecov/codecov-action@v3
  with:
    files: tests/_output/coverage.cov
```
