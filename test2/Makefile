APP=laravel_app

up:
	docker-compose up -d

build:
	docker-compose up -d --build
	docker exec -it $(APP) composer install
	docker exec -it $(APP) sh -c 'if [ ! -f .env ]; then cp .env.example .env; fi'
	docker exec -it $(APP) php artisan key:generate

down:
	docker-compose down

bash:
	docker exec -it $(APP) bash

test:
	docker exec -it $(APP) php artisan test

code-check:
	docker exec -it $(APP) composer code-check