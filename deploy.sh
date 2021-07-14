#!/bin/bash

mkdir logs
touch logs/error.log
touch logs/access.log
chmod -R 777 logs

mkdir tmp
mkdir tmp/queries
chmod -R 777 tmp
