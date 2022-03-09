# 다음
- composer global require laravel/installer

# 다음
- laravel new site10
- cd site10

# 다음
- .env 파일 수정
- APP_URL=http://localhost:8000
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=site10
- DB_USERNAME=root
- DB_PASSWORD=

# 다음
- MariaDB 서버 켜기

# 다음
- /c/xampp/mysql/bin/mysql -u sbsst -psbs123414 -e "DROP DATABASE IF EXISTS site10; CREATE DATABASE site10"

# 다음
- ./artisan migrate

# 다음
- composer require laravel/telescope
- ./artisan telescope:install
- ./artisan migrate

# 다음
- config/auth.php
- sanctum을 드라이버로 사용하는 api 가드 생성

# 다음
- ./artisan make:middleware UseApiGuard
- Auth::shouldUse('api');
- 'useapiguard' => \App\Http\Middleware\UseApiGuard::class,
- 'useapiguard',

# 다음
- php artisan make:controller API/V1/AuthController