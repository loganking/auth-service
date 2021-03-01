# Authentication Service Api

After registering by posting a user with a name, email, and password to '/api/v1/user', you can login at '/api/v1/login'. Then with the returned token string, you can pass that as an 'x-api-token' header in subsequent requests to authenticate when updating, deleting, or logging out.

## Set up
- Start the services with `docker-compose up -d`
- Install dependencies with `docker-compose exec phpfpm composer install`
- Run migrations to initialize database with `docker-compose exec phpfpm php artisan migrate`
- The auth service should be running at localhost:80 (if you have port conflicts, you can change port in docker-compose.yml on line 21)

## Running tests
- After the stack is running, initialize the testing db with `docker-compose exec db mysql -proot -e "CREATE DATABASE auth_service_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;GRANT ALL PRIVILEGES ON  auth_service_test. * TO 'admin'@'%';"`
- Run tests with `docker-compose exec phpfpm ./vendor/bin/phpunit`

When trying to initialize test db, you may get errors because mysql is not ready. This can be resolved by making a web request to the application and then trying again.

## Next Steps if more time
- OpenAPI documentation
- Better error handling
- Authorization to be able to update / delete user records (maybe with an additional super user role)
- Endpoints for other services to authenticate / authorize users
