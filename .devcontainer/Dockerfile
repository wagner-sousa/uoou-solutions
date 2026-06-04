FROM php:8.5-apache-trixie

# Install PostgreSQL client + base tooling
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y --no-install-recommends \
        build-essential libpq-dev postgresql-client \
        git unzip libzip-dev zlib1g-dev libicu-dev pkg-config ca-certificates autoconf curl \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed by Symfony + PostgreSQL
RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_pgsql intl

# Install Xdebug via PECL
RUN pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get update && apt-get install -y symfony-cli

RUN apt-get clean -y && rm -rf /var/lib/apt/lists/*
# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

