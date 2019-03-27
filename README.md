# Kontrolgruppen

## Setup for development

```sh
./install.sh
composer install
bin/console doctrine:migrations:migrate
```

## Encore

The project uses Symfony Encore to handle js and scss. Run the following to 
```sh
yarn run encore dev --watch
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
