# Kontrolgruppen development setup

## Checkout submodules
```sh
git submodule update --init --recursive --remote
```

## Starting the show

```sh
docker-compose pull
docker-compose up -d
```

Open the site in your default browser:

```sh
open http://$(docker-compose port nginx 80)
```

## Setup for development

```sh
# Install dependencies.
composer install

# Migrate database.
# If using docker these bin/console commands should be run from inside the phpfpm container.
docker-compose exec phpfpm /app/bin/console doctrine:migrations:migrate

# Create super admin user.
docker-compose exec phpfpm /app/bin/console fos:user:create --super-admin
```

## Coding standards

Check Symfony coding standards using [PHP Coding Standards
Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and
[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer):

```sh
composer check-coding-standards
```

Apply Symfony coding standards:

```sh
composer apply-coding-standards
```

### Twig (experimental)

Check Twig templates using [Twigcs](https://github.com/allocine/twigcs):

```sh
composer check-coding-standards/twigcs
```
