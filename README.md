# ![Laravel RealWorld Example App](.github/readme/logo.png)

> Example of a PHP-based Laravel application containing real world examples (CRUD, auth, advanced patterns, etc) that adheres to the [RealWorld](https://github.com/gothinkster/realworld) API spec.

This codebase was created to demonstrate a backend application built with [Laravel framework](https://laravel.com/) including RESTful services, CRUD operations, authentication, routing, pagination, and more.

We've gone to great lengths to adhere to the **Laravel framework** community style guides & best practices.

For more information on how to this works with other frontends/backends, head over to the [RealWorld](https://github.com/gothinkster/realworld) repo.

## How it works

The API is built with [Laravel](https://laravel.com/), making the most of the framework's features out-of-the-box.

The application is using a custom JWT auth implementation: [`app/Jwt`](laravel-api/app/Jwt).

The App is built with VueJs3

## Getting started

The preferred way of setting up the project is using [Laravel Sail](https://laravel.com/docs/sail),
for that you'll need [Docker](https://docs.docker.com/get-docker/) under Linux / macOS (or Windows WSL2).

### Installation

Start the containers with PHP application and PostgreSQL database:

    ./rig up -d

Migrate the database with seeding:

    ./rig artisan migrate --seed

### Run tests

    ./rig artisan test

### Run PHPStan static analysis

    ./rig php ./vendor/bin/phpstan
