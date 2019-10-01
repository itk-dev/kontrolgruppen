# Kontrolgruppen development setup

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

Add core-bundle to the project:
```sh
git clone git@github.com:aakb/kontrolgruppen-core-bundle.git bundles/core-bundle
```

Change configuration in composer.json to use the local copy of the core-bundle: (make sure that the version constraint of the package is set to the current branch your local clone of the core bundle)
```json
"require": {
	"kontrolgruppen/core-bundle": "dev-develop",
},
"repositories": {
    "kontrolgruppen/core-bundle": {
        "type": "path",
        "url": "bundles/kontrolgruppen-core-bundle",
        "options": {
            "symlink": true
        }
    }
},
```

```sh
# Install dependencies.
composer install

# Migrate database.
# If using docker these bin/console commands should be run from inside the phpfpm container.
docker-compose exec phpfpm /app/bin/console doctrine:migrations:migrate

# Create super admin user.
docker-compose exec phpfpm /app/bin/console fos:user:create --super-admin
```

## Use maker bundle

To use MakerBundle in Kontrolgruppen\CoreBundle, use the following environment variable:

```
MAKER_NAMESPACE=Kontrolgruppen\\CoreBundle php bin/console make:entity
```

This will place the files in the correct location.

## Building js assets

Watch for changes in js and css files and build development version:
```
yarn watch
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

### JavaScript

Check JavaScript files using [eslint](https://eslint.org/):

```sh
yarn check-coding-standards-js
```

Apply coding standards:

```sh
yarn apply-coding-standards-js
```

### SCSS

Check SCSS files using [stylelint](https://stylelint.io/):

```sh
yarn check-coding-standards-scss
```

Apply coding standards:

```sh
yarn apply-coding-standards-scss
```
