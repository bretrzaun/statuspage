FROM php:8.3-cli
RUN apt-get update -y && apt-get upgrade -y && apt-get install unzip git libssl-dev -y
RUN pecl install mongodb \
	&& docker-php-ext-enable mongodb
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN echo 'alias c="composer"' >> ~/.bashrc
WORKDIR /app