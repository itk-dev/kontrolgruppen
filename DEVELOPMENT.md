# Development

The `docker compose` setup uses a custom image hosted on GitHub, and you have to
sign in to download this image.

Go to <https://github.com/settings/tokens/new> and create a new personal access
token with `read:packages` checked. Save the token in a file, e.g.
`~/github-docker-read-token.txt`.

Run this command to sign in using your token before pulling docker images
(replace `USERNAME` with your actual GitHub username):

```sh
cat ~/github-docker-read-token.txt | docker login https://docker.pkg.github.com -u USERNAME --password-stdin
```

Build the project:

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
```

Run migrations:

```sh
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

Run fixtures:

```sh
docker compose exec phpfpm bin/console doctrine:fixtures:load --no-interaction
```

Sign in as admin with one time token:

```sh
# Sign in as the admin@example.com user.
docker compose exec --env ROUTER_REQUEST_CONTEXT_HOST=$(docker compose port nginx 8080) phpfpm bin/console kontrolgruppen:user:login admin@example.com
```
