# Worpress Test Instance insiede single Container
FROM debian:jessie
MAINTAINER Philipp Wagner "philipp.wagner@incowia.com"
ENV REFRESHED_AT 2016_05_20

#----------------------------- apache2 ------------------------------#
# install Apache and create directory for wordpress sources
RUN apt-get update && apt-get install -y apache2 \
  && mkdir /var/www/wordpress

# copy site configuration
COPY ./apache2/000-default.conf /etc/apache2/sites-available/

#----------------------------- php5.6 --------------------------------#
# install php5
RUN apt-get install -y \
  php5 \
  libapache2-mod-php5


# for testing purposes
COPY ./info.php /var/www/wordpress

# give apache permission to write /var/www/wordpress
RUN chown -R www-data:www-data /var/www/wordpress



EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
