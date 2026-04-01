.PHONY: it
it: refactoring coding-standards security-analysis static-code-analysis tests ## Runs the refactoring, coding-standards, security-analysis, static-code-analysis, and tests targets

.PHONY: code-coverage
code-coverage: docker-build ## Collects code coverage from running unit tests with phpunit/phpunit
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php81-xdebug -c "/app/src/.docker/code-coverage.sh"

.PHONY: coding-standards
coding-standards: docker-build vendor ## Lints YAML files with yamllint, normalizes composer.json with ergebnis/composer-normalize, and fixes code style issues with friendsofphp/php-cs-fixer
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "yamllint --config-file .yamllint.yaml --strict ."
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "composer normalize --ansi"
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "vendor/bin/php-cs-fixer fix --ansi --config=.php-cs-fixer.php --diff --show-progress=dots --verbose"

.PHONY: dependency-analysis
dependency-analysis: docker-build vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "PHIVE_HOME=.build/phive phive install --trust-gpg-keys 0x2DF45277AEF09A2F,0x033E5F8D801A2F8D"
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c ".phive/composer-require-checker check --ansi --config-file=/app/composer-require-checker.json --verbose"

.PHONY: docker-build
docker-build: docker-lint ## Builds Docker images
	docker image build --quiet --tag phpunit-agent-reporter-php81 .docker/php81/
	docker image build --quiet --tag phpunit-agent-reporter-php81-xdebug .docker/php81-xdebug/
	docker image build --quiet --tag phpunit-agent-reporter-php82 .docker/php82/
	docker image build --quiet --tag phpunit-agent-reporter-php83 .docker/php83/
	docker image build --quiet --tag phpunit-agent-reporter-php84 .docker/php84/

.PHONY: docker-lint
docker-lint: ## Lints Dockerfiles with hadolint
	docker container run --rm --interactive hadolint/hadolint < .docker/php81/Dockerfile
	docker container run --rm --interactive hadolint/hadolint < .docker/php81-xdebug/Dockerfile
	docker container run --rm --interactive hadolint/hadolint < .docker/php82/Dockerfile
	docker container run --rm --interactive hadolint/hadolint < .docker/php83/Dockerfile
	docker container run --rm --interactive hadolint/hadolint < .docker/php84/Dockerfile

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: mutation-tests
mutation-tests: docker-build vendor ## Runs mutation tests with infection/infection
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81-xdebug -c "vendor/bin/infection --ansi --configuration=infection.json"

.PHONY: phar
phar: docker-build vendor ## Builds a phar with humbug/box
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c ".docker/phar.sh"

.PHONY: refactoring
refactoring: docker-build vendor ## Runs automated refactoring with rector/rector
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "vendor/bin/rector --ansi --config=rector.php"

.PHONY: security-analysis
security-analysis: docker-build vendor ## Runs a security analysis with composer
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "composer audit --ansi"

.PHONY: static-code-analysis
static-code-analysis: docker-build vendor ## Runs a static code analysis with phpstan/phpstan
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "vendor/bin/phpstan --ansi --configuration=phpstan.neon --memory-limit=-1"

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: docker-build vendor ## Generates a baseline for static code analysis with phpstan/phpstan
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "vendor/bin/phpstan --ansi --allow-empty-baseline --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon --memory-limit=-1"

.PHONY: tests
tests: tests-unit tests-end-to-end tests-phar ## Runs unit, end-to-end, and phar tests with phpunit/phpunit

.PHONY: tests-end-to-end
tests-end-to-end: docker-build ## Runs end-to-end tests with phpunit/phpunit in Docker containers
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "/app/src/.docker/tests-end-to-end.sh 10.0.0 lowest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "/app/src/.docker/tests-end-to-end.sh 10.0.0 highest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php82 -c "/app/src/.docker/tests-end-to-end.sh 11.0.0 lowest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php82 -c "/app/src/.docker/tests-end-to-end.sh 11.0.0 highest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php83 -c "/app/src/.docker/tests-end-to-end.sh 12.0.0 lowest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php83 -c "/app/src/.docker/tests-end-to-end.sh 12.0.0 highest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php84 -c "/app/src/.docker/tests-end-to-end.sh 13.0.0 lowest"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php84 -c "/app/src/.docker/tests-end-to-end.sh 13.0.0 highest"

.PHONY: tests-phar
tests-phar: phar docker-build ## Runs phar tests with phpunit/phpunit in Docker containers
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "/app/src/.docker/tests-phar.sh 10.0.0"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php82 -c "/app/src/.docker/tests-phar.sh 11.0.0"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php83 -c "/app/src/.docker/tests-phar.sh 12.0.0"
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php84 -c "/app/src/.docker/tests-phar.sh 13.0.0"

.PHONY: tests-unit
tests-unit: docker-build ## Runs unit tests with phpunit/phpunit in a Docker container
	docker container run --rm --volume $(CURDIR):/app/src:ro --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "/app/src/.docker/tests-unit.sh"

vendor: docker-build composer.json composer.lock ## Installs dependencies with composer
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "composer validate --ansi --strict"
	docker container run --rm --volume $(CURDIR):/app --volume composer-cache:/root/.composer phpunit-agent-reporter-php81 -c "composer install --ansi --no-interaction --no-progress"
