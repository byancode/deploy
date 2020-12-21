# Laravel Deploy

perform a push in git and a pull in your server

## Getting Started

### 1. Install

Run the following command:

```bash
composer require byancode/deploy
```

### 2. Register (for Laravel > 6.0)

Register the service provider in `config/app.php`

```php
Byancode\Deploy\Providers\DeployProvider::class,
```

### 3. Publish

Publish config file.

```bash
php artisan vendor:publish --provider="Byancode\Deploy\Providers\DeployProvider"
```

### 4. Configure

You can change the options of your app from `config/deploy.php` file

## Usage

```bash
php artisan deploy:git --commit="My first deploy"
```
