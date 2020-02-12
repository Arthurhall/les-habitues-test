-include .env

define main_title
	@{ \
    set -e ;\
    msg="Make $@";\
    echo "\n\033[34m$$msg" ;\
    for i in $$(seq 1 $${#msg}) ; do printf '=' ; done ;\
    echo "\033[0m\n" ;\
    }
endef

sf-console: ## Run interactive symfony command
	$(call main_title,)
	@read -p "command [options] [arguments]: " command; \
	bin/console $$command;

##@ Utils
.PHONY: assets
assets: ## Generate assets using webpack
	$(call main_title,)
	yarn run dev

assets-watch: ## Generate assets using webpack in continuous (watch mode)
	$(call main_title,)
	yarn run watch

assets-build: ## Generate assets using webpack for production
	$(call e,)
	 yarn run build

cache: ## Clear and warmup cache
	$(call main_title,)
	bin/console --ansi cache:clear

requirements-checker: ## Check Symfony requirements
	$(call main_title,)
	bin/requirements-checker

lint-yaml: ## Checks that the YAML config files contain no syntax errors
	$(call main_title,)
	bin/console lint:yaml config src --parse-tags

lint-twig: ## Checks that the Twig template files contain no syntax errors
	$(call main_title,)
	bin/console lint:twig templates src --env=prod

##@ Database

.PHONY: db
db: ## Drop and create database and tables and import fixtures
db: db-drop db-create db-create-tables fixtures
db-force: ## Drop force and create froce database and tables and import fixtures
db-force: db-drop-force db-create db-create-tables-force fixtures

db-create: ## Create database
	$(call main_title,)
	bin/console --ansi doctrine:database:create --if-not-exists

db-create-tables: ## Create tables with doctrine schema update
	$(call main_title,)
	bin/console --ansi doctrine:schema:update

db-create-tables-force: ## Create database with force
	$(call main_title,)
	bin/console --ansi doctrine:schema:update --force

db-drop: ## Drop database
	$(call main_title,)
	bin/console --ansi doctrine:database:drop --if-exists

db-drop-force: ## Drop database with force
	$(call main_title,)
	bin/console --ansi doctrine:database:drop --if-exists --force

fixtures: ## Play fixtures (database will be purged)
	$(call main_title,)
	bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate

fixtures-append: ## Play fixtures in append mode (no database purged)
	$(call main_title,)
	bin/console doctrine:fixtures:load --append

db-migrate: ## Execute a migration to a specified version or the latest available version
	$(call main_title,)
	bin/console doctrine:migrations:migrate --no-interaction

##@ Tests

.PHONY: tests
tests: ## Launch a set of tests
tests: phpunit php-cs phpstan

phpstan: ## Launch phpstan tests
	$(call main_title,)
	bin/phpstan analyse --level 7 src

phpunit: ## Launch phpunit tests
	$(call main_title,)
	./bin/simple-phpunit --debug tests

php-cs: ## Launch php-cs without fixing
	$(call main_title,)
	bin/php-cs-fixer fix -v --show-progress=estimating-max --diff-format=udiff --dry-run --config .php_cs.dist

##@ Helpers

.PHONY: help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
.DEFAULT_GOAL := help
