<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Mukellef Backend Challenge Case Study
## Developer Notes
- .env file tracked/pushed intentionally
- Developed on Windows 11 - WSL2 - Ubuntu Subsystem

# Requirements
- Stable version of [Docker](https://docs.docker.com/engine/install/)
- Compatible version of [Docker Compose](https://docs.docker.com/compose/install/#install-compose)

# How To Deploy

### For first time only !
- `git clone https://github.com/ErayerA/sdp-api.git`
- `cd sdp-api`
- `docker compose up -d --build`
- `docker compose exec phpmyadmin chmod 777 /sessions`
- `docker compose exec php bash`
- `chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache`
- `chmod -R 775 /var/www/storage /var/www/bootstrap/cache`
- `composer setup`

### From the second time onwards
- `docker compose up -d`

# IMPORTANT:

### Don't forget to run artisan, composer, etc. commands in php container. Jump there by:
- `docker compose exec php bash`

### Don't forget to run queue
- `php artisan queue:listen`

### Fix ownership & permissions of files created by artisan:
- `sh owners.sh`

## Postman Instructions
1. Fill `client_secret` envrionment variable with the same field's value of the only existing row in `oauth_clients` db table
2. Run `GetAccessToken` request first and let the token to be saved to environment.

# Notes


### Anytime for a fresh start
- `php artisan migrate:fresh --seed`

### Command for renewing subscriptions
- `php artisan app:renew-subscriptions`


### Postman Collection
- `<project_root>/SDP.postman_collection.json`

### Postman Rough API Docs
- [Published Docs](https://documenter.getpostman.com/view/36980497/2sA3kRH3EY#9e051e9d-3c87-4a66-bf8f-d6e2f2568e56)

### Laravel App
- URL: http://localhost

### Mailpit
- URL: http://localhost:8025

### phpMyAdmin
- URL: http://localhost:8080
- Server: `db`
- Username: `mukellef`
- Password: `mukellef`
- Database: `main`


