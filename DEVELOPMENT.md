# Development

```sh
docker network create frontend
docker compose pull
docker compose up --detach
docker compose exec phpfpm composer install
# The var folder may be owned by root. Remove it.
rm -fr var
docker compose exec phpfpm bin/console cache:clear

# Build CSS and JS assets using https://symfony.com/doc/current/frontend.html#frontend-webpack-encore
docker compose run --rm node yarn install
docker compose run --rm node yarn build

echo "http://$(docker compose port nginx 8080)"
```

```sh
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

```sh
docker compose exec phpfpm bin/console doctrine:fixtures:load --no-interaction
```

```sh
# Sign in as the admin@example.com user.
docker compose exec --env ROUTER_REQUEST_CONTEXT_HOST=$(docker compose port nginx 8080) phpfpm bin/console kontrolgruppen:user:login admin@example.com
```
