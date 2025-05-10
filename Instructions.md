## Usage Instructions
1. Create the necessary directories:
2. Create the configuration files mentioned above in their respective directories.
3. Build and start the containers:
4. Install Laravel dependencies:
5. Generate application key:
bash

Run

Open Folder

1

docker-compose exec app php artisan

key:generate

6. Run migrations:
bash

Run

Open Folder

1

docker-compose exec app php artisan migrate

This Docker setup provides:

- PHP 8.2 with necessary extensions for Laravel
- Nginx web server
- MySQL 8.0 database
- Proper networking between containers
- Volume mapping for persistent data
You can customize the environment variables, ports, and other settings according to your specific requirements.