# beauty-parlor-api
Built with Laravel 


1. clone the repo `git clone https://github.com/Chisonm/beauty-parlor-api.git`
2. cd into the project project folder
3. To install the dependencies run `composer install`
4. run `php artisan key:generate`
5. Migrate the database `php artisan migrate`
6. to run test `php artisan test --testsuite=Feature` or `./vendor/bin/phpunit`
7. To start the server `php artisan serve`
8. To run the application on localhost `http://localhost:8000/api/v1/`

For code style fixer, pint has been integrated `https://laravel.com/docs/9.x/pint`

Run `./vendor/bin/pint` to run pint

## API Endpoints

| Endpoint | Functionality |
| --- | --- |
| POST /api/v1/signup | Register a user |
| POST /api/v1/signin | Login a user |
| GET /api/v1/users | Fetch all users |
| PUT /api/v1/user/{id} | update a user status |
| apiResource /api/v1/manage-shops | crud operations for admin to manage shops |
| GET api/v1/shops/{shop} | get all shop for authenticated user |
| POST api/v1/create-shop | create shop


## API Documentation

Postman Collection Link: https://www.getpostman.com/collections/8900e37ea9106090e970

Postman Documentaion Link: https://documenter.getpostman.com/view/15635438/2s83maNmEw

Swagger Documentaion: https://beautyparlorapi.furniturestorex.com/api/docs

