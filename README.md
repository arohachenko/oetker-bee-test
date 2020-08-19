[![Build Status](https://travis-ci.org/arohachenko/oetker-bee-test.svg?branch=develop)](https://travis-ci.org/arohachenko/oetker-bee-test)
[![codecov](https://codecov.io/gh/arohachenko/oetker-bee-test/branch/develop/graph/badge.svg)](https://codecov.io/gh/arohachenko/oetker-bee-test)

## OetkerDigital code challenge

Hello! This is an attempt at the code challenge for Senior Backend Engineer position.

The stack:
* Symfony 5
* PHP 7.4
* MySQL 5.7
* Nginx + php-fpm
* All running in Docker

This is a pretty conventional stack, which should be more than enough for the job.

Data structure is based on 2 schemas: `record` and `artist`. The separation was done mostly to introduce more challenge, but it also allows for a reverse search: fetching the artists through the API and getting the record set for each of them.

Because of this, there's a set of endpoints to deal with records, and also a matching set for artists.
Artist endpoints are there for consistency, and some edge use cases. Normally they can be ignored though, since the artist will also be created by the `POST record` call.

As the API could potentially be exposed to the world (or some unreliable parties), it requires authentication.
An example authentication is implemented here using JWT, and a mock API endpoint to "authenticate" you with the identity of your choice.
In real world, the issuing of tokens would be separate from our application.

## Requirements

The application requires Docker 19.03.0+ and docker-compose to run.

###### Windows

On Windows machine, it's also recommended to have:
* Git for Windows (to get Git Bash)
* "make" for Git Bash. (https://gist.github.com/evanwill/0207876c3243bbb6863e65ec5dc3f058#make)

## Running the application

Go to the project directory and run
```shell script
make start
```
This will build the app containers, if not available, and start them. It will install dependencies via composer, and start accepting requests.

But it's not fully ready yet. Wait a few seconds for MySQL server to boot up, and run
```shell script
make migrate
```
This will execute the necessary DB migrations.
 
The application now runs on your local docker host (http://host.docker.internal on Windows, or http://localhost on Linux/maxOS by default).

You should see the Api Doc interface.

###### Useful commands

```shell script
make phpunit    # run the phpunit test suite
make populate   # prefill the database with some sample data
make shell      # open a command shell into php-fpm container
make status     # show running containers
make stop       # shut down the environment
```
Run `make info` for all available commands.

## Calling the API

It is critical to keep in mind that the app requires a valid JWT token, which you get by calling `GET /api/login_check/user` or `GET /api/login_check/admin`.
Assuming you use the Nelmio API Doc interface, copy the resulting token (without quotes!) and paste it into the "Authorize" dialog, in this format:
```
Bearer yourTokenHere
```
This will add `Authorization: Bearer yourTokenHere` header to all your future calls.

Keep in mind, that only admin has access to POST/PUT/DELETE methods.
