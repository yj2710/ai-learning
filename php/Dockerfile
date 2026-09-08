FROM php:8.3-fpm-bookworm

ARG DEBIAN_FRONTEND=noninteractive

ENV TZ=Asia/Shanghai
ENV COMPOSER_HOME=/root/composer
ENV PATH="${COMPOSER_HOME}/vendor/bin:${PATH}"

# ============================================================
# 1. 时区
# ============================================================
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

# ============================================================
# 2. Debian Bookworm 国内镜像
# PHP 8.3 不再使用 PHP 7.4 的 Buster 源
# ============================================================
RUN sed -i 's@deb.debian.org@mirrors.aliyun.com@g' /etc/apt/sources.list.d/debian.sources \
    && sed -i 's@security.debian.org@mirrors.aliyun.com@g' /etc/apt/sources.list.d/debian.sources

# ============================================================
# 3. 系统依赖
# ============================================================
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        apt-utils \
        zip unzip vim wget curl git ca-certificates pkg-config \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
        zlib1g-dev \
        libicu-dev \
        libpq-dev \
        libmagickwand-dev \
        libssl-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libxslt1-dev \
        libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# ============================================================
# 4. GD
# ============================================================
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm

# ============================================================
# 5. PHP 官方扩展
# 保留你 PHP 7.4 的常用扩展，并增加 PostgreSQL
# ============================================================
RUN docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        sockets \
        opcache \
        zip \
        bcmath \
        calendar \
        exif \
        gettext \
        pcntl \
        intl \
        mbstring \
        curl \
        xml \
        xsl

# ============================================================
# 6. PECL 扩展
# ============================================================
RUN pecl channel-update pecl.php.net \
    && pecl install igbinary \
    && pecl install redis \
    && pecl install mongodb \
    && pecl install imagick \
    && pecl install swoole \
    && docker-php-ext-enable \
        igbinary \
        redis \
        mongodb \
        imagick \
        swoole

# ============================================================
# 7. PHP 配置
# ============================================================
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY php.ini /usr/local/etc/php/conf.d/runtime.ini

# ============================================================
# 8. Composer
# ============================================================
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer \
    && composer config -g repos.packagist composer https://mirrors.aliyun.com/composer/

# ============================================================
# 9. 工作目录
# ============================================================
WORKDIR /var/www/html

# 与宿主机常见开发用户保持一致，避免挂载目录产生 root 文件
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

CMD ["php-fpm"]
