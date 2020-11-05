## Bagpack

Organize your bulky migration files into tabled directories.

Ever worked on a large laravel project with huge migration files, and it's a mess
trying to figure out all the related migration files specific to a table?

That's what Bagpack does. It allows you to group your migration files 
into directories so you can easily find all the histories related to a particular schema table.

#### Before Backpack
![alt text](before-bagpack.png?raw=true)

#### After Backpack
![alt text](after-bagpack.png?raw=true)

## Installation.

Install via composer: 

```composer require nwogu/bagpack```

You're all set and ready. All your migration generations will now be
grouped into tabled directories.

## Configurations.

You may optionally publish the config file by running:  

```php artisan vendor:publish --tag bagpack```  

## Packaging Migration Files.

To package existing migration files into tabled directories, run:

```php artisan migration:package``` 

You can optionally pass a ```--path``` flag to specify a full path to your
migration directory. Defaults to database/migrations.

## Disbaling Bagpack

You can disable bagpack from running by setting ```run``` to false in your config file