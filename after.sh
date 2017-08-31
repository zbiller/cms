#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.



if [ ! -f /usr/local/dependencies_installed ]; then

    echo 'Installing extra dependencies...'

    # Go to the "Code" directory mapped in Homestead.yaml
    cd ~/Code

    # Install zsh
    sudo apt-get install zsh -y

    # Install ffmpeg
    sudo apt-get install ffmpeg -y

    # Install jpegoption
    sudo apt-get install jpegoptim

    # Install optipng
    sudo apt-get install optipng

    # Install pngquant
    sudo apt-get install pngquant

    # Install svgo
    sudo npm install -g svgo

    # Install gifsicle
    sudo apt-get install gifsicle

    # Install phpmyadmin

    echo 'Downloading phpMyAdmin 4.7.4'
    sudo curl -#L https://files.phpmyadmin.net/phpMyAdmin/4.7.4/phpMyAdmin-4.7.4-english.tar.gz -o pma.tar.gz
    sudo mkdir pma
    sudo tar xzfv pma.tar.gz -C pma --strip-components 1
    sudo rm -rf pma.tar.gz
    sudo bash /vagrant/vendor/laravel/homestead/scripts/create-certificate.sh pma.dev
    sudo bash /vagrant/vendor/laravel/homestead/scripts/serve-laravel.sh pma.dev $(pwd)/pma
    sudo ln -s /var/run/php/php7.1-fpm.sock /var/run/php/php-fpm.sock

    # Restart Nginx
    sudo service nginx reload

    # Set a flag to know that the extra software is installed
    sudo touch /usr/local/dependencies_installed

    echo 'Finished installing extra dependencies.'
else
    echo "Extra dependencies already installed. Moving on..."
fi