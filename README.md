# Address Book

Simple address book. Allows to register new users, add/edit address book records and share them with other users.


## Requirements

This project can be fully run using Docker.

## How to run

```shell script
# Start web server
docker-compose up -d php

# Wait for database to initialize
sleep 5

# Install composer dependencies
docker-compose exec php composer install -o

# Run database migrations
docker-compose exec php php bin/console doctrine:migrations:migrate -n
```

 * After this you can access the web server at http://localhost:8000
 * The API documentation is available at http://localhost:8000/api

## API usage

```shell script
# Set API address
export API_ENDPOINT=http://localhost:8000/api
export COOKIE_JAR=var/cache/cookie.txt

# Create new user (will also log-in if not previously authenticated)
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"email":"user1@example.com","password":"password123"}' ${API_ENDPOINT}/users

# Retrieve current user
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X GET ${API_ENDPOINT}/users/me

# Create some contacts
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"name":"My Friend #1","phone":"+1-541-754-3010"}' ${API_ENDPOINT}/contacts
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"name":"My Friend #2","phone":"+1-541-754-3011"}' ${API_ENDPOINT}/contacts
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"name":"My Friend #3","phone":"+1-541-754-3012"}' ${API_ENDPOINT}/contacts

# Retrieve user's own contacts
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X GET ${API_ENDPOINT}/contacts

# Replace contact
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X PUT -H "Content-Type: application/json" --data '{"name":"My Friend #3","phone":"+1-541-754-3012"}' ${API_ENDPOINT}/contacts/2

# Modify contact
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X PATCH -H "Content-Type: application/merge-patch+json" --data '{"name":"My Very Good Friend #3"}' ${API_ENDPOINT}/contacts/2

# Create another user
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"email":"user2@example.com","password":"password123"}' ${API_ENDPOINT}/users

# Share some contacts
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"contact":"/api/contacts/2","sharedWith":{"email":"user2@example.com"}}' ${API_ENDPOINT}/shared_contacts/share_with_email

# Retrieve contacts shared by user
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X GET ${API_ENDPOINT}/shared_contacts

# Logout
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST ${API_ENDPOINT}/auth/logout

# Login with another user
curl -sb ${COOKIE_JAR} -c ${COOKIE_JAR} -X POST -H "Content-Type: application/json" --data '{"email":"user2@example.com","password":"password123"}' ${API_ENDPOINT}/auth/login

# Retrieve current user
curl -sb ${COOKIE_JAR}  -X GET ${API_ENDPOINT}/shared_contacts
```

## Running tests
```shell script
# Start test database
docker-compose up -d db-test

# Wait for database to initialize
sleep 5

# Run phpunit
docker-compose run php-test bin/phpunit
```

## Potential improvements

 * Add user interface (not done due to time constraints.)
 * Email confirmation after registration.
 * Notifications after contact is shared/unshared (need email sending configured.)
 * More strict uniqueness requirements for contacts (currently allows duplicate records.)
 * More comprehensive contact fields (email, address, etc.)
