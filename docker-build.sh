#!/bin/sh
cd docker/mysql
docker build -t cubx-wp-db:0.1 .
cd ../wordpress
docker build -t cubx-wp:0.1 .
cd ../..
