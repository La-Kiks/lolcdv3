# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

db-ip: ## Get the IP of the DB to connect
	@docker inspect lolcdv3-database-1 | grep IPAddress

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

fixtures-load: ## Load the fixtures, by default the load command purges the database. Add c='--append' to append instead
	@$(eval c ?=)
	@$(SYMFONY) doctrine:fixtures:load $(c)

## —— Project 🚀 ——————————————————————————————————————————————————————————————
tailwind: ## activate the tailwind watch
	@$(call GREEN,"The application is available for dev https://localhost")
	@$(SYMFONY) tailwind:build --watch

dev: up tailwind ## Start to dev !

## —— Doctrine & DB 🔥 ———————————————————————————————————————————————————————————————
db-init: db-drop ## Drop and create the database
	@$(SYMFONY) doctrine:database:create
	@$(SYMFONY) doctrine:schema:create

db-drop: ## Drop the database
	$(SYMFONY) doctrine:database:drop --if-exists --force -vv; \

db-create: ## Create the database using Symfony Doctrine command
	@$(SYMFONY) doctrine:database:create -vv

db-diff: ##
	@$(SYMFONY) doctrine:migrations:diff

db-schema: ##
	@$(SYMFONY) doctrine:migrations:dump-schema

db-update: ## Update the database
	@$(SYMFONY) make:migration --formatted
	@$(SYMFONY) doctrine:migrations:migrate

db-test: ## Drop and create the test database and load fixtures
	@$(SYMFONY) --env=test doctrine:database:drop --if-exists --force
	@$(SYMFONY) --env=test doctrine:database:create
	@$(SYMFONY) --env=test doctrine:schema:create
	@$(SYMFONY) --env=test doctrine:fixtures:load --no-interaction

