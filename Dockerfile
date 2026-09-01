FROM flyimg/base-image:1.8.2

COPY .    /var/www/html

#add www-data + mdkdir var folder
RUN usermod -u 1000 www-data && \
    mkdir -p /var/www/html/var/tmp web/uploads/.tmb && \
    chown -R www-data:www-data var/  web/uploads/ && \
    chmod 777 -R var/  web/uploads/

RUN composer install --no-dev --optimize-autoloader

COPY cleanup-tmp.sh cleanup-tmp.sh
RUN chmod +x cleanup-tmp.sh
RUN ./cleanup-tmp.sh
