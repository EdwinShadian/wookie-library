all: composer laravel migrate

composer:
	composer install

laravel:
	php artisan key:generate & php artisan jwt:secret && php artisan storage:link

migrate:
	php artisan migrate --seed && php artisan migrate --database=test
