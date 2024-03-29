FROM ministryofjustice/wordpress-base:php7.4

ADD . /bedrock

WORKDIR /bedrock

ARG COMPOSER_USER
ARG COMPOSER_PASS

# Set execute bit permissions before running build scripts
RUN chmod +x bin/* && sleep 1 && \
    make clean && \
    bin/composer-auth.sh && \
    make build && \
    rm -f auth.json
