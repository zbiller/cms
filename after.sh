#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.



PHP_VERSION=7.1
ELASTICSEARCH_VERSION=5.5.2
KIBANA_VERSION=5.5.2



if [ ! -f /usr/local/first_time ]; then
    echo 'Installing extra software...'



    # Go to the "Code" directory mapped in Homestead.yaml
    cd ~/Code



    # Install video manipulation tool for uploading functionality
    sudo apt-get install ffmpeg -y



    # Install image optimization tools

    sudo apt-get install jpegoptim
    sudo apt-get install optipng
    sudo apt-get install pngquant
    sudo npm install -g svgo
    sudo apt-get install gifsicle



    # Install PhpMyAdmin

    sudo curl -#L https://files.phpmyadmin.net/phpMyAdmin/4.7.4/phpMyAdmin-4.7.4-english.tar.gz -o pma.tar.gz
    sudo mkdir /usr/share/pma
    sudo tar xfv pma.tar.gz -C /usr/share/pma --strip-components 1
    sudo rm -rf pma.tar.gz

    sudo bash /vagrant/vendor/laravel/homestead/scripts/create-certificate.sh pma.dev
    sudo bash /vagrant/vendor/laravel/homestead/scripts/serve-laravel.sh pma.dev /usr/share/pma
    sudo ln -s /var/run/php/php$PHP_VERSION-fpm.sock /var/run/php/php-fpm.sock

    sudo service nginx reload



    # Install Java

    sudo apt-get update
    sudo apt-get install default-jre -y



    # Install ElasticSearch

    sudo wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
    sudo apt-get update
    sudo wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-$ELASTICSEARCH_VERSION.deb
    sudo dpkg -i elasticsearch-$ELASTICSEARCH_VERSION.deb
    sudo rm -rf elasticsearch-$ELASTICSEARCH_VERSION.deb

    sudo sed -i 's/#bootstrap.memory_lock: true/bootstrap.memory_lock: true/' /etc/elasticsearch/elasticsearch.yml

    sudo service elasticsearch restart
    sudo update-rc.d elasticsearch defaults 95 10



    # Install Kibana

    sudo wget https://artifacts.elastic.co/downloads/kibana/kibana-$KIBANA_VERSION-amd64.deb
    sudo dpkg -i kibana-$KIBANA_VERSION-amd64.deb
    sudo rm -rf kibana-$KIBANA_VERSION-amd64.deb

    sudo sed -i 's/#server.host: "localhost"/server.host: "192.168.10.10"/' /etc/kibana/kibana.yml

    sudo update-rc.d kibana defaults 95 10



    # Install and configure Supervisor

    sudo apt-get install supervisor -y
    sudo touch /etc/supervisor/conf.d/worker.conf

    echo '[program:worker]' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'process_name=%(program_name)s_%(process_num)02d' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'command=php /home/vagrant/Code/artisan queue:work --sleep=3 --tries=3' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'autostart=true' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'autorestart=true' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'user=vagrant' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'numprocs=3' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'redirect_stderr=true' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null
    echo 'stdout_logfile=/home/vagrant/Code/storage/logs/queue.log' | sudo tee --append /etc/supervisor/conf.d/worker.conf > /dev/null

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start worker:*



    # Disable filemode in Git

    git config core.filemode false



    # Grant directory permissions

    sudo chmod -R 775 storage/
    sudo chmod -R 775 bootstrap/cache/



    # Generate an application key

    php /home/vagrant/Code/artisan key:generate



    # Migrate the database

    php /home/vagrant/Code/artisan migrate



    # Seed the database

    php /home/vagrant/Code/artisan db:seed



    # Symlink uploads directories

    php /home/vagrant/Code/artisan uploads:link



    # Set a flag to know that the extra software is installed

    sudo touch /usr/local/first_time



    echo 'Finished installing extra software.'
else
    echo "Extra software already installed. Moving on..."
fi