# Eloquent Raw Sql

laravel model eloquent to raw sql

## Getting Started

### 1. Install

Run the following command:

```bash
composer require byancode/eloquent-raw-sql
```

```php
Byancode\EloquentRawSql\Provider::class,
```

### 2. Publish

Publish config file.

```bash
php artisan vendor:publish --provider="Byancode\EloquentRawSql\Provider"
```


### 3. Example
```php
App\User::where('id', 41545)->orderBy('id', 'desc')->toRawSql();
# output: select * from `users` where `users`.`id` = 41545 order by `users`.`id` DESC
```