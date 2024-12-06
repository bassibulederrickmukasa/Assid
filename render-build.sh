#!/usr/bin/env bash

# Download Composer
curl -sS https://getcomposer.org/installer | php

# Install project dependencies
php composer.phar install
