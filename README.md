# Document Royal Class Assignment

This project is a Laravel-based application for managing document-related tasks. It includes various modules and is
 set up to handle dependencies effectively. It leverages Laravel's powerful features for a robust and secure application.

## Requirements

- PHP: ^8.1
- Laravel: ^10.10
- MySQL (or any other database compatible with Laravel)
- Composer
- Redis (for queue management)

## Installation

Follow these steps to set up the project on your local machine.

### 1. Clone the repository

Clone the repository to your local machine:

```bash
git clone https://github.com/alaeldin91/document_royal_class_assignement.git

2. Install dependencies
Navigate to the project directory and run Composer to install the necessary dependencies:

cd document_royal_class_assignement
composer install

3. Set up environment variables
Copy the .env.example file to .env:


cp .env.example .env

Edit the .env file to configure the database and other services.
Example configuration:
env
نسخ الكود
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:d1HBcnEDgPlRn2Fx4Nc7DqXkSyTthOq1eIMQvd7/7yI=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=document
DB_USERNAME=root
DB_PASSWORD=

4. Generate application key
Generate the application key by running:

php artisan key:generate

5. Migrate the database
Run the migrations to set up the database:

php artisan migrate

6. Serve the application
Start the local development server:
php artisan serve

Now, you can access the application at http://localhost:8000.
Development


Running Migrations for Modules
If your project uses modules with their own migrations, you can migrate specific modules or all modules using the custom migrate:modules command:
Migrate all enabled modules with dependencies:

php artisan migrate:modules

Migrate a specific module:

php artisan migrate:modules {module_name}

Queue Management
The project uses Redis for queue management. To start processing the queues, run:

php artisan queue:work

Configuration
.env
The .env file contains essential configurations for the application, including database and Redis configurations. Make sure you update this with the correct credentials for your environment.
Encryption Keys
Encryption keys for different modules are provided in the .env file for secure storage and transmission.

GENERAL_ENCRYPTION_KEY=HKikiLbOqxsRUkt3OsyzYWPW4iootZlJ3Uet+XdGqLA=
MOTORS_ENCRYPTION_KEY=Oij+gPn6sLMc+72zM0cfOr9AksFhz2RDVq5pn00JktU=
JOBS_ENCRYPTION_KEY=2tbVe4wQAEBHSlCWl3oCI3l5L/DA+ZYqV8jB5TdbO50=

Redis Configuration
Ensure that Redis is properly set up in the .env file:
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_CLIENT=predis

License
This project is licensed under the MIT License - see the LICENSE file for details.
Acknowledgements
Laravel Framework: https://laravel.com
Redis: https://redis.io
Composer: https://getcomposer.org

### How to Use:

1. **Installation**: This section provides a step-by-step guide to install the project and get it running on your local machine, including cloning the repository, installing dependencies, and setting up the environment variables.
   
2. **Development Setup**: This section explains how to run the tests, use your custom `migrate:modules` command to migrate database modules, and handle queues with Redis.

3. **Configuration**: The configuration section provides important details about how to modify the `.env` file for environment-specific setups (like database connections and Redis) and encryption keys for various modules.

4. **License and Acknowledgments**: Standard open-source MIT license and credits to Laravel, Redis, and Composer for their contributions.

This document should serve as a comprehensive guide for both developers and contributors

