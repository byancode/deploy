# Laravel Deploy

perform a push in Github and a pull in your server

## Getting Started

### Requisites

* [PHP extension ssh2](https://pecl.php.net/package/ssh2)

### 1.1. Install servidor

Run the following command:

``` bash
yum install git
git config --global user.name "username"
git config --global user.email "my@github.email"
```

### 1.2. Generate SSH public and private keys 

* [Generating ssh keys github](https://docs.github.com/es/free-pro-team@latest/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent)

``` bash
ssh-keygen -t rsa -b 4096 -C "my@github.email"
```

### 1.3. Register SSH public key

* [Github ssh keys](https://github.com/settings/keys)

### 1.4. Clone project

``` bash
cd /www/repositories
git clone git@github.com:username/repository.git
```

### 2.1. Install Laravel

Run the following command:

``` bash
composer require byancode/deploy
```

### 2.2. Register (for Laravel > 7.0)

Register the service provider in `config/app.php`

``` php
Byancode\Deploy\Providers\DeployProvider::class,
```

### 2.3. Publish

Publish config file.

``` bash
php artisan vendor:publish --provider="Byancode\Deploy\Providers\DeployProvider"
```

### 2.4. Configure

You can change the options of your app from `config/deploy.php` file

## Usage

 `*Note: only laravel git project`

``` bash
php artisan deploy:run --commit="My first deploy" --git --yarn --composer
```
