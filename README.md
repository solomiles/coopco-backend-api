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
3. press `ctrl + o` press `ctrl + o`, then `ctrl + x` and press `Enter` afterwrds, then `ctrl + x`
4. run `exit`

### Mac
1. run `nano ~/.zshrc`
2. paste `alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'` at the bottom of the file
3. press `ctrl + o` press `ctrl + o`, then `ctrl + x` and press `Enter` afterwrds, then `ctrl + x`
4. run `exit`

### Docker Setup
- make sure WAMP/XAMPP/LAMP or any application using port **80** and post **5432** is not running

- Make sure [docker desktop](https://www.docker.com/products/docker-desktop/) is running
- open a new terminal and run `sail up`, wait for the images to download
- When the download is done, open localhost on your browser to confirm that the site is up and running 🚀


**PS:** Subsequently just run `sail up` to start the application ✨