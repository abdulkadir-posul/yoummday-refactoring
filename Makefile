.PHONY: install run test coverage analyse shell build

build:
	docker compose build

install:
	docker compose run --rm app composer install

run:
	docker compose up app

test:
	docker compose run --rm test

coverage:
	docker compose run --rm app vendor/bin/phpunit --coverage-text Test

analyse:
	docker compose run --rm app vendor/bin/phpstan analyse

shell:
	docker compose run --rm app bash
