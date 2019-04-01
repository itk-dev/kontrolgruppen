# Kontrolgruppen development setup

## Setup for development

```sh
# Install dependencies.
composer install

# Get the kontrolgruppen bundles to packages/.
./scripts/develop/install.sh

# Create composer-dev.json and install the local kontrolgruppen/* bundles.
./scripts/develop/develop.sh

# Migrate database.
bin/console doctrine:migrations:migrate

# Create super admin user.
bin/console fos:user:create --super-admin
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
