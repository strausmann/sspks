FROM gitpod/workspace-full

USER root

# gitpod trick to bypass the docker caching mechanism for all lines below this one
# just increment the value each time you want to bypass the cache system
ENV INVALIDATE_CACHE=1

# Update APT Database
### base ###
RUN sudo apt-get update -q \
    && sudo apt-get install -y php-dev ca-certificates rsync grc shellcheck \
    && sudo apt-get clean

# Create log files and move required files to their proper locations
RUN sudo touch /var/log/xdebug.log \
    && sudo chmod 666 /var/log/xdebug.log

# Install XDebug
RUN wget http://xdebug.org/files/xdebug-3.1.1.tgz \
    && tar -xvzf xdebug-3.1.1.tgz \
    && cd xdebug-3.1.1 \
    && phpize \
    && ./configure \
    && make \
    && sudo mkdir -p /usr/lib/php/20200930 \
    && sudo cp modules/xdebug.so /usr/lib/php/20200930

# Copy xdebug config
COPY --chmod=666 gitpod/.gp/xdebug.ini /etc/php/8.0/cli/conf.d/20-xdebug.ini

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
ENV PATH="$PATH:/home/gitpod/.config/composer/vendor/bin:$GITPOD_REPO_ROOT/vendor/bin"
