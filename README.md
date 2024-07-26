# Webshop API

## Requirements

- [Docker](https://www.docker.com/products/docker-desktop/)

## Getting Started

### Setting up the project

1. `docker compose up`
2. Open a new terminal tab after docker finishes building and starts up the containers
3. `docker compose exec php /bin/bash -c "composer install"` - install composer dependencies
4. Visit http://localhost:3000/up to make sure the API is running
5. [Use this Postman collection to interact with the API](https://app.getpostman.com/run-collection/37253815-bf4d36d4-7d70-4a8b-8e9e-a57636392e08?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D37253815-bf4d36d4-7d70-4a8b-8e9e-a57636392e08%26entityType%3Dcollection%26workspaceId%3D64c4086d-ac80-4bf6-ad17-717aa879b10c#?env%5Blocal%5D=W3sia2V5IjoiYmFzZV91cmwiLCJ2YWx1ZSI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9hcGkiLCJlbmFibGVkIjp0cnVlLCJ0eXBlIjoiZGVmYXVsdCJ9XQ==)

## Working with the project

Once you have the project setup you can use `docker compose run` to run commands in a new Laravel container, or `docker compose exec` to run commands in an existing container if `docker compose up` is already running.

Use `docker compose exec php /bin/bash` to login into the running Laravel container to run artisan commands below.

### Example commands

- `php artisan --version` - check laravel version
- `./vendor/bin/pint` - run code formatting
- `php artisan cache:clear` - clear cache

### Tests

There are two sets of tests, functional and performance, and also two corresponding seeders. The seeders are separated because performance seeder seeds a ton of data and takes a few minutes to complete.

#### Functional Tests

- `php artisan migrate:fresh --seed` - drop existing tables, run migrations, seed functional data
- `php artisan test --testsuite=Feature` - run functional tests
- `php artisan test --testsuite=Feature -d --update-snapshots` - run functional tests and update snapshots

#### Performance Tests

- `php artisan migrate:fresh --seed --seeder=PerformanceDataSeeder` - drop existing tables, run migrations, seed performance data, this will take a few minutes
- `php artisan test --testsuite=Performance` - run performance tests

## Database

Two DB dumps are available, `docker/db/entrypoint/2024-07-26-functional.sql.gz` and `docker/db/2024-07-26-performance.sql.gz`. By default when you run `docker compose up` functional dump will be imported. If you want performance dump imported first stop docker, then `rm -rf docker/db/data` and then swap the files.

EER diagram excluding Laravel defaul/system tables: https://dbdiagram.io/d/webshop-api-66a375a68b4bb5230e6e4cb9
