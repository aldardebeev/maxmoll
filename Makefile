.PHONY: build
build:
	docker compose build

.PHONY: up
up:
	docker compose up -d

.PHONY: down
down:
	docker compose down
#--env-file ./../money-tracking-app/.env down

.PHONY: ps
ps:
	docker compose ps

.PHONY: bash
bash:
	docker compose exec app bash

.PHONY: restart
restart:
	docker compose down && docker compose build && docker compose up -d

 .PHONY: migrate
migrate:
	docker compose exec app php artisan migrate

 .PHONY: seed
seed:
	docker compose exec app php artisan db:seed
