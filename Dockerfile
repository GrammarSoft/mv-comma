FROM debian:stretch

LABEL vendor="GrammarSoft ApS" \
	maintainer="Tino Didriksen <mail@tinodidriksen.com>" \
	com.grammarsoft.product="Kommaforslag Frontend" \
	com.grammarsoft.codename="comma-frontend"

ENV LANG=C.UTF-8 \
	LC_ALL=C.UTF-8 \
	DEBIAN_FRONTEND=noninteractive \
	DEBCONF_NONINTERACTIVE_SEEN=true

COPY ./ /var/www/html/

RUN apt-get update && \
	apt-get install -y --no-install-recommends \
		apache2 \
		libapache2-mod-php \
		php-json \
		php-curl \
		ca-certificates \
	&& \
	phpenmod json && \
	phpenmod curl && \
	a2enmod env && \
	a2enmod php7.0 && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/* && \
	rm -rf /var/www/html/.git && \
	echo "PassEnv DEBUG_KEY COMMA_HOST COMMA_PORT DANPROOF_URL MVID_SERVICE MVID_SECRET MVID_ACCESS_IDS CADUCEUS_URL CADUCEUS_SECRET GOOGLE_AID" > /etc/apache2/conf-enabled/passenv.conf && \
	ln -sf /dev/stderr /var/log/apache2/error.log && \
	ln -sf /dev/stdout /var/log/apache2/access.log

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
