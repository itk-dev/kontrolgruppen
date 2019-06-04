# Kontrolgruppen development setup

## Getting the submodules
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

## Note about working with git submodules
When checking out submodules with the git submodule update command, the checked
out branch will be to a detached head pointing to the submodule commit latest pushed in the supermodule.
For example if the submodule is on a feature branch, and that change is tracked and pushed in the supermodule, next
time someone checks out the supermodule and updates the submodule, the submodule will point to that feature branch.

So make sure that the submodule is pointing to the right branch, when tracking and pushing in the supermodule. Finish
changes in the submodule before tracking and pushing in the supermodule!

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
