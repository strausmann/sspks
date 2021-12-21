FROM gitpod/workspace-full

USER root

# Update APT Database
### base ###
RUN sudo apt-get update -q \
    && sudo apt-get install -y php-dev ca-certificates 

# Install XDebug
RUN wget http://xdebug.org/files/xdebug-3.1.1.tgz \
    && tar -xvzf xdebug-3.1.1.tgz \
    && cd xdebug-3.1.1 \
    && phpize \
    && ./configure \
    && make \
    && sudo mkdir -p /usr/lib/php/20200930 \
    && sudo cp modules/xdebug.so /usr/lib/php/20200930 \
    && sudo bash -c "echo -e '\nzend_extension = /usr/lib/php/20200930/xdebug.so\n[XDebug]\nxdebug.client_host = 0.0.0.0\nxdebug.client_port = 9003\nxdebug.log = /var/log/xdebug.log\nxdebug.mode = debug\nxdebug.start_with_request = yes\n' >> /etc/php/8.0/cli/conf.d/20-xdebug.ini"

# Install Krypton
RUN sudo curl https://krypt.co/kr | sh

# Install latest composer v2 release
RUN curl -s https://getcomposer.org/installer | php \
    && sudo mv composer.phar /usr/bin/composer \
    && sudo mkdir -p /home/gitpod/.config \
    && sudo chown -R gitpod:gitpod /home/gitpod/.config

USER gitpod

# Install Changelogger
RUN COMPOSER_ALLOW_SUPERUSER=1 composer global require churchtools/changelogger; exit 0

# Add composer bin folder to $PATH
ENV PATH="$PATH:/home/gitpod/.config/composer/vendor/bin"
