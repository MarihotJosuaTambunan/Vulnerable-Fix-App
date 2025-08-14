# Sistem Pencatatan dan Management Keuangan
Sistem ini adalah aplikasi web yang dirancang sebagai sarana edukasi untuk memahami praktik keamanan siber, khususnya terkait kerentanan OWASP Top 10 A01: Broken Access Control. Aplikasi ini dikembangkan menggunakan framework Laravel 10 dan antarmuka penggunanya dibangun dengan Bootstrap 5

## Tech
- [Laravel 10](https://laravel.com/) - The PHP Framework for Web Artisans
- [Bootstrap 5](https://getbootstrap.com/) - Build fast, responsive sites with Bootstrap
- [Font Awesome](https://fontawesome.com/) - Take the hassle out of icons in your website.

## Installation
Laravel 10.x requires a minimum PHP version of 8.1 to run.

##### Git Clone:
```sh
git clone Vulnerable-App https://github.com/marihottambunan/Vulnerable-Fix-App.git
```

##### Change Directory Into Project:
```sh
cd sistem-informasi-keuangan
```

##### Install Composer Dependencies, to install Vendor file Laravel:
```sh
composer install
```

##### Install NPM dependencies
```sh
npm install
```

##### Copy .env file:
```sh
cp .env.example .env
```

##### Generate an app encryption key:
```sh
php artisan key:generate
```

##### Migrate the database:
```sh
php artisan migrate
```

##### Seed the database (optional):
```sh
php artisan db:seed
```

##### Install Laravel Sanctum:
```sh
composer require laravel/sanctum
```

##### Run Laravel:
```sh
php artisan serve
```
##### Akun Pengguna Default, Setelah menjalankan php artisan db:seed, Anda dapat menggunakan akun-akun berikut untuk login:

| Email                                                          | Password | Role            |
| -------------------------------------------------------------- | -------- | --------------- |
| [admin@gmail.com](mailto:admin@gmail.com)                      | test123  | Admin           |
| [finance\_manager@gmail.com](mailto:finance_manager@gmail.com) | test123  | Finance Manager |
| [test@gmail.com](mailto:test@gmail.com)                        | test123  | Karyawan        |
| [karyawan@gmail.com](mailto:karyawan@gmail.com)                | test123  | Karyawan        |

