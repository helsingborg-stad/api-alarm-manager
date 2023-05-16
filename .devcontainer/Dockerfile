FROM mcr.microsoft.com/devcontainers/php:0-7.4-bullseye

# Install php-mysql driver
RUN docker-php-ext-install mysqli pdo pdo_mysql ssh2

# Update expired key
# RUN export YARNKEY=yarn-keyring.gpg && \
#     curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo gpg --dearmour -o /usr/share/keyrings/$YARNKEY &&\
#     echo \"deb [signed-by=/usr/share/keyrings/$YARNKEY] https://dl.yarnpkg.com/debian stable main\" | sudo tee /etc/apt/sources.list.d/yarn.list