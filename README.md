# API example in PHP

### Configure the application

Create the database and user for the project:

```
mysql -uroot -p
CREATE DATABASE api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'api_admin'@'localhost' identified by 'api_admi_password';
GRANT ALL on api.* to 'api_admin'@'localhost';
quit
```

Copy and edit the `.env` file and enter your database details:

```
cp .env.example .env
```

Install the project dependencies and start the PHP server:

```
composer install
cd public
php -S 127.0.0.1:8000
```

Loading [127.0.0.1:8000/user](127.0.0.1:8000/user) should return a 401 Unauthorized response now.

### Get the token

In the public directory, simply run:

```
php token.php
```

or call "Get Token" API call from the postman collection "user_api.postman_collection.json"

### Swagger

Open [http://127.0.0.1:8000/web/index.html](http://127.0.0.1:8000/web/index.html) to see all API calls.

### Postman

Open Postman and import "user_api.postman_collection.json" collection to use API.