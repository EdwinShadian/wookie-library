# Test Task "Wookie Library"

## Summary

### Description

This is a backend library application. It can provide both public and internal APIs for customers and authors. It uses JWT authorization and user's roles for permissions. Also it has some administrative features (you will see their description below).

### Used technologies

    - PHP 8.1
    - Laravel 10
    - Laravel Sail
    - Postgres 15
    - Docker

Also I used **Psalm**, **PHPUnit** and **PHP CS Fixer** as a code quality tools.

## How to deploy project locally

You need Docker and Linux/WSL for the deployment.

1. First of all you need to copy *.env.example* to *.env*:

    ```shell
    cp .env.example .env
    ```

    This file is almost ready for the local deployment. It will have its changes automatically on next step.

2. Now you need to start docker containers:

    ```shell
    docker compose up -d --build
    ```

3. When containers are started, you can init application using **Make**:

    ```shell
    docker exec -it wookie-library-app-1 make
    ```

    **Make** will automatically procced through all steps which application is needed. Also it seeds some preinstall users and roles for them.

4. Now you're ready for a testing! You can start tests using command:

    ```shell
    docker exec -it wookie-library-app-1 vendor/bin/phpunit tests
    ```

    Also you can add some books to the library using command:

    ```shell
    docker exec -it wookie-library-app-1 php artisan app:get-books <number of books>
    ```

    It can take some time if you want thousands of books at once.

### API routes

Below I will use *italic* for non-required params and **bold** for required ones.

1. **Auth and registration**
    - **POST** /api/auth/login - login to the system and take your JWT token
        - **author_pseudonym** - as a name
        - **password**
    - **POST** /api/auth/register - create a new user with *Author* role (about roles below)
        - **name** - author's name
        - **author_pseudonym** - nickname for the author. Must be unique
        - **password**
    - **GET** /api/auth/me - provide user details about himself
    - **POST** /api/auth/logout - logout from system
    - **POST** /api/auth/refresh - refresh your token before it expires

2. **Public API** (it doesn't require any authorization, but it's functionality is limited)
    - **GET** /api/public/books - get paginated list of books
        - *q* - search query (only for book's title)
        - *perPage* - a number of books on a single page
        - *page* - a number of page you want to get
    - **GET** /api/public/books/{id} - get book's details

3. **Internal API** (it provides a basic CRUD operations for authors)
    - **GET** /api/internal/books - get paginated list of books. It works just as a public one
    - **GET** /api/internal/books/{id} - get book's details. It works just as a public one
    - **POST** /api/internal/books - publish new book to the library. You need to be a *Publisher*
        - **title** - title for a book
        - **description** - short book's summary
        - **price** - book's price
        - *author* - original author of the book. If it wasn't provide then the author is you
        - *cover* - .jpg or .png image for the cover
    - **PUT/PATCH** /api/internal/books/{id} - update book's properties. You need to be a *Publisher* of this book or an *Admin*. List of parameters is the same as for the publishing.
    - **DELETE** /api/internal/books/{id} - unpublish book from the library. You need to be a *Publisher* of this book or an *Admin*.

4. **Admin API** (it provides some admin features such as role change, scrolling users list, ban user from using internal API)
    - **GET** /api/admin/users - get paginated list of users
        - *perPage* - a number of books on a single page
        - *page* - a number of page you want to get
    - **POST** /api/admin/roles - change user's roles
        - **user_id** - user's id
        - **roles** - an array with roles' names which you want to give to the user. User can be an **admin**, an **author** (he's a basic user of internal API) and a **publisher** (he has permissions to publish, update and delete books, but only those which related to him).
    - **POST** /api/admin/ban/{user_id} - to ban user = to take off his roles and rights for using internal API
