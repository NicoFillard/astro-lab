# Install project

After clone the project go to the directory : **cd astro-lab/**
Use command : **composer install**

Duplicate the file **.env** et rename it to **.env.local** for connection to the database.

# Database

Use : **php bin/console doctrine:database:create** to create the database
Then : **php bin/console doctrine:schema:update --force** to create the schema of database
And use : **php bin/console doctrine:fixtures:load** to add fixtures in database

# Docker

Use command : **docker-compose build** to build images for php and nginx container
And then : **docker compose up** to launch all containers
