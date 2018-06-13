# Builder
FROM node:8 as builder
WORKDIR /app
COPY ./ /app

# Environment
ENV APP_ENV prod

RUN npm install --unsafe-perm --production

# Service
FROM nginx:1.13
COPY ./etc/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=builder /app/html /usr/share/nginx/html
