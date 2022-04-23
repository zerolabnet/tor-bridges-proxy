FROM alpine:3.15.4

ENV TZ=Europe/Moscow

RUN echo '@edgecommunity https://dl-cdn.alpinelinux.org/alpine/edge/community' >> /etc/apk/repositories && \
    echo '@testing http://dl-cdn.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories && \
    apk -U upgrade && \
    apk -v add --no-cache tor@edgecommunity obfs4proxy@testing bash curl nginx php8-fpm php8-session apache2-utils && \
    rm -rf /var/cache/apk/* && \
    chmod 700 /var/lib/tor && \
    mkdir -p /var/www && \
    chown tor:root /var/www/

COPY --chown=tor:root torrc /etc/tor/
COPY --chown=tor:root bridges.txt /etc/tor/
COPY --chown=tor:root nginx.conf /etc/nginx/
COPY --chown=tor:root php-fpm.conf /etc/php8/
COPY --chown=tor:root www.conf /etc/php8/php-fpm.d/
COPY --chown=tor:root bridges.sh /srv/
COPY --chown=tor:root pwd.sh /srv/
COPY --chown=tor:root tor-bridges-proxy /srv/
COPY --chown=tor:root webroot/ /var/www/

RUN chmod +x /srv/bridges.sh && \
    chmod +x /srv/pwd.sh && \
    chmod +x /srv/tor-bridges-proxy

HEALTHCHECK --timeout=10s --start-period=60s \
CMD curl --fail --socks5-hostname 127.0.0.1:9150 -I -L 'https://www.facebookwkhpilnemxj7asaniu7vnjjbiltxjqhye3mhbshg7kx5tfyd.onion/' || exit 1

USER tor

EXPOSE 9053/udp 9150/tcp 9151/tcp

CMD ["/srv/tor-bridges-proxy"]