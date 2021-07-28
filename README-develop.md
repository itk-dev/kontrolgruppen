# Kontrolgruppen development setup

## Starting the show

The `docker-compose` setup uses a custom image hosted on GitHub, and you have to
sign in to download this image.

Go to <https://github.com/settings/tokens/new> and create a new personal access
token with `read:packages` checked. Save the token in a file, e.g.
`~/github-docker-read-token.txt`.

Run this command to sign in using your token before pulling docker images
(replace `USERNAME` with your actual GitHub username):

```sh
cat ~/github-docker-read-token.txt | docker login https://docker.pkg.github.com -u USERNAME --password-stdin
```

to sign in (cf.
<https://docs.github.com/en/packages/guides/configuring-docker-for-use-with-github-packages#authenticating-with-a-personal-access-token>).

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

# Use the recently checked out bundle in the composer setup:
composer config repositories.kontrolgruppen/core-bundle path bundles/core-bundle
composer require 'kontrolgruppen/core-bundle:*'
```

It's very important that you don't commit the changed composer.json and composer.lock file, as they now are in a development version.

```sh
# Install dependencies.
composer install

# Migrate database.
# If using docker these bin/console commands should be run from inside the phpfpm container.
docker-compose exec phpfpm /app/bin/console doctrine:migrations:migrate

# Create super admin user.
docker-compose exec phpfpm /app/bin/console fos:user:create --super-admin
```

## Fixtures

You can load fixtures by running the command

```sh
docker-compose exec phpfpm bin/console doctrine:fixtures:load
```

and then use

```sh
docker-compose exec phpfpm bin/console kontrolgruppen:user:login admin@example.com
```

to get a one-time sign in url.

### CPR Service
```sh
# Make sure that the database is created
docker-compose exec borgerdata node createdb.js

# Migrate database
docker-compose exec borgerdata yarn knex migrate:latest

# Seed the database
docker-compose exec borgerdata yarn knex seed:run
```


## Use maker bundle

To use MakerBundle in Kontrolgruppen\CoreBundle, use the following environment variable:

```
MAKER_NAMESPACE=Kontrolgruppen\\CoreBundle php bin/console make:entity
```

This will place the files in the correct location.

## Building js assets

Watch for changes in js and css files and build development version:

```sh
docker run --volume ${PWD}:/app --workdir /app node:10 yarn install
docker run --interactive --tty --volume ${PWD}:/app --workdir /app node:10 yarn watch
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

### GitHub Actions

All code checks mentioned above are automatically run by [GitHub
Actions](https://github.com/features/actions) when a pull request is created.

To run the actions locally, install [act](https://github.com/nektos/act) and run

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request
```

Use `act -P ubuntu-latest=shivammathur/node:focal pull_request --list` to see
individual workflow jobs that can be run, e.g.

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request --job phpcsfixer
```
