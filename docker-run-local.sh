#!/bin/sh

docker run -d -p 3306:3306 --name cubx-wp-db cubx-wp-db:0.1 &&
docker run -d -p 8080:80 --name cubx-wp --link cubx-wp-db:mysql cubx-wp:0.1 &&
docker run -d -p 8081:80 --name cubx-wp-myadmin --link cubx-wp-db:mysql -e PMA_HOST=cubx-wp-db phpmyadmin/phpmyadmin
