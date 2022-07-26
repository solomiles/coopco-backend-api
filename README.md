<p align="center"><a href="https://coopco.com.ng" target="_blank"><img src="https://coopco.com.ng/assets/images/logo.png" width="200"></a></p>


## About Coopco

Coopco is a cloud-based ERP solution that is created to handle all business related tasks of a Cooperative Society.

Our ultimate goal is to deliver a scalable/flexible custom solution for all types of Cooperative Societies with components that are needed to run every management and financial process. We aim at #Simplicity; making your daily operation efficient with real-time reports and centralized management.

---
## Application Setup
- Install [docker](https://docs.docker.com/get-docker/) and [docker desktop](https://www.docker.com/products/docker-desktop/)
- Install [composer](https://getcomposer.org/download/)
- Clone this repository (if you have not already done that)
- In the project directory run `composer install`


### Laravel Sail Setup
- run `composer require laravel/sail --dev`
- run `php artisan sail:install`
### Windows
1. run `doskey '[ -f sail ] && bash sail || bash C:/path-to-project/vendor/bin/sail'`
2. run `exit`
### Linux
1. run `nano ~/.bashrc`
2. paste `alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'` at the bottom of the file
3. press `ctrl + o` and press `Enter` afterwards, then `ctrl + x`
4. run `exit`

### Mac
1. run `nano ~/.zshrc`
2. paste `alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'` at the bottom of the file
3. press `ctrl + o` and press `Enter` afterwards, then `ctrl + x`
4. run `exit`

Open a new terminal and type `sail` to make sure the command has been added.
### Docker Setup
- make sure WAMP/XAMPP/LAMP or any application using port **80** and post **5432** is not running

- Make sure [docker desktop](https://www.docker.com/products/docker-desktop/) is running
- open a new terminal and run `cp .env.example .env` then `sail up`, wait for the images to download
- When the download is done, open localhost on your browser to confirm that the site is up and running 🚀


**PS:** Subsequently just run `sail up` to start the application ✨

---
## PostgreSQL Dynamic Schema Architecture
Every cooperative will own their own db schema to help us separate and organize data properly.
The auth token sent to the web frontend when an admin logs in will be composed using the admin object, the object will contain the schema that admin belongs to.
Now, when a request is made we can pull the schema from that admin object and modify the app config to use the specified schema.
This is how we can acheive that, `config(['database.connections.pgsql.search_path' => 'schema-name']);`.

### Setting up the database
-	Download a database management client like [TablePlus](https://tableplus.com/download), [Sequel Pro](https://www.sqlprostudio.com/), [DBeaver](https://dbeaver.io/download/), or any other one you're comfortable with.
-	If you were able to setup Laravel Sail create a new connection and fill on the following credentials:
	1. host: 127.0.0.1
	2. port: 5432
	3. username: sail
	4. password: password
	5. database: laravel
- When you have successfuly connected, create a new database titled `coopco` and switch to it

- If you were not able to setup Laravel Sail and you installed postgres separately, you'll need to start the postgres server and connect using the provided credentials when you setup postgres. Use the db client to connect and create the `coopco` database.