
### Installation

1. Install composer dependencies
```
$ composer install
```

2. Configure .env file, edit file with
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourdatabase
DB_USERNAME=user
DB_PASSWORD=password
```
3. Generate APP_KEY
```
$ php artisan key:generate
```

4.Create client
```
$php artisan passport:install
```

5.Run migrations
```
$php artisan migrate

```
6. Run server
```
$ php artisan serve
```

### Routes

    -POST /signup
    -POST /login
    -GET /logout
    -GET /profile
    -RESOURCE /tweets
    -GET /users
    -GET /users/{user}/follow
    -GET /users/{user}/unFollow
    -GET /tweet/{tweet}/likeUnlike
    -GET /tweet/{tweet}/likedMe
    -POST /tweet/{tweet}/comments
    -DELETE /tweet/delete/{comment}
    -GET /timeline
