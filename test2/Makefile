APP=laravel_app

up:
	docker-compose up -d

build:
	docker-compose up -d --build

down:
	docker-compose down

bash:
	docker exec -it $(APP) bash

key-generate:
	docker exec -it $(APP) php artisan key:generate

test:
	docker exec -it $(APP) php artisan test
