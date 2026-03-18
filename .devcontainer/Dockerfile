FROM mcr.microsoft.com/devcontainers/php:3-8.5-trixie

# Install MySQL client + base tooling for Laravel
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y default-mysql-client git unzip libzip-dev zlib1g-dev \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed by Laravel + MySQL
RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get update && apt-get install -y symfony-cli

RUN apt-get clean -y && rm -rf /var/lib/apt/lists/*
# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

