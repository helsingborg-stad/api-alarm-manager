FROM wordpress:6.2

FROM mcr.microsoft.com/vscode/devcontainers/php:7.4

# Copy WordPress files from wordpress container.
COPY --from=0 /usr/src/wordpress/ /var/www/html/

# Make vscode owner of all WordPress files.
RUN chown -R vscode:vscode /var/www/html

# Update expired key that would otherwise cause apt-get update to fail.
RUN export YARNKEY=yarn-keyring.gpg && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo gpg --dearmour -o /usr/share/keyrings/$YARNKEY && \
    echo "deb [signed-by=/usr/share/keyrings/$YARNKEY] https://dl.yarnpkg.com/debian stable main" | sudo tee /etc/apt/sources.list.d/yarn.list

# Install packages
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install --no-install-recommends curl nano subversion bash-completion 

# Install php-mysql driver
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable apache mods
RUN a2enmod rewrite expires

# Install wp cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && sudo mv wp-cli.phar /usr/local/bin/wp