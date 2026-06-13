# Deployment Guide

This project supports Docker-based deployment and can be wired into CI/CD with GitHub Actions without a container registry.

## What is included

- `docker-compose.yml` for a production-oriented stack
- `Dockerfile` using `php-fpm` for Laravel
- nginx in front of PHP-FPM
- dedicated queue worker and scheduler containers
- MySQL container for single-server deployment
- PHP production settings and OPCache enabled

## Services

- `nginx`: public web server
- `app`: Laravel application running on PHP-FPM
- `queue-worker`: processes queued jobs
- `scheduler`: runs `php artisan schedule:run` every minute
- `db`: MySQL 8

## Server requirements

- Docker Engine
- Docker Compose plugin
- a Linux VPS or server
- a domain name pointed to the server

## First-time setup

1. Copy the project to your server.
2. Copy `.env.production.example` to `.env`.
3. Update every placeholder value in `.env`, especially:
   - `APP_URL`
   - `APP_KEY`
   - `DB_PASSWORD`
   - `DB_ROOT_PASSWORD`
   - mail settings
   - Google API settings
4. Generate an application key if you do not want the container to auto-generate one:

```bash
docker compose run --rm app php artisan key:generate --show
```

5. Put the generated value into `.env` as `APP_KEY=...`.

## Deploy

Build and start the stack:

```bash
docker compose up -d --build
```

Run database migrations:

```bash
docker compose exec app php artisan migrate --force
```

Cache Laravel for production:

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan route:cache
```

If `route:cache` fails because a route uses a Closure, skip that command.

## Updates

After pulling new code to the server:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan view:cache
```

## Recommended CI/CD flow

Use GitHub Actions to:

1. run automated checks
2. connect to the VPS over SSH
3. clone or update the repository on the server
4. run `docker compose up -d --build`
5. run Laravel migrations and cache warmup

The repository already includes an example workflow at `.github/workflows/cicd.yml`.

### Required GitHub secrets

- `DEPLOY_HOST`
- `DEPLOY_USER`
- `DEPLOY_SSH_KEY`

### Required server files

The recommended deploy path is:

```bash
/opt/miracle-institute
```

Keep the production `.env` file in that folder.

Create or update `.env` with at least:

```env
APP_PORT=8082
```

If you already have services on `8080` and `8081`, `8082` is a safe default based on the current server snapshot.

### First server bootstrap

Create the folder once:

```bash
mkdir -p /opt/miracle-institute
```

Then let GitHub Actions populate or update the repository there on each deploy.

### Port notes

- This stack only publishes the web service port from `nginx`.
- MySQL is internal-only in this compose file, so it does not consume a host port.
- The default public port is `8082` to avoid collisions with existing services already using `8080` and `8081`.
- If you later place host nginx, Caddy, or Traefik in front of the app, proxy traffic to `127.0.0.1:8082`.

## Useful commands

View logs:

```bash
docker compose logs -f nginx
docker compose logs -f app
docker compose logs -f queue-worker
docker compose logs -f scheduler
```

Open a shell inside the app container:

```bash
docker compose exec app sh
```

Restart services:

```bash
docker compose restart
```

Stop the stack:

```bash
docker compose down
```

## HTTPS

This stack exposes the container on port 80 and publishes it to host port `8082` by default. For HTTPS, put the server behind:

- a host-level nginx reverse proxy with Let's Encrypt, or
- a gateway like Traefik or Caddy

If you want, we can add one of those next.

## Notes

- `docker-compose.dev.yml` remains the development setup.
- `docker-compose.yml` is now intended for deployment.
- `RUN_MIGRATIONS=true` is available, but manual migration is safer for production.
