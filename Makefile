all: composer laravel migrate

build: copy composer laravel migrate

copy:
    cp .env.example .env

composer:
	composer install

migrate:
	php artisan migrate --seed && php artisan migrate --database=test

laravel:
	php artisan key:generate & php artisan jwt:secret && php artisan storage:link
