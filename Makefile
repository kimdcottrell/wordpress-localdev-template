#!make
sinclude .env

.PHONY: help

.DEFAULT_GOAL := help

init: ## Initial standup of project
	docker compose up --build -d
	docker compose run --rm cli wp core install --title="WordPress Test" --admin_user=admin --admin_password=password --admin_email=test@test.com --skip-email --url=https://${SERVER_NAME}

nuke: ## Kill your entire system's Docker containers and prune all containers, images, and volumes
	docker kill $$(docker ps -aq) 
	docker system prune -af --volumes

composer:
	docker compose run --rm composer bash

coffee: ## Get your terminal caffeinated
	echo $$'\342\230\225\012'

# This will output the help for each task
# thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Magic terminal on my screen, what is the fairest help menu of them all?
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)


