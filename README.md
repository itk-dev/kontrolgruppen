# Kontrolgruppen

## Install

Run

```sh
./bin/install --env=prod
```

to install in production.

### Update

```sh
bin/console doctrine:migrations:list
bin/console doctrine:migrations:sync-metadata-storage
bin/console doctrine:migrations:version --add --all --no-interaction
bin/console doctrine:migrations:list
```

(cf. <https://github.com/itk-dev/documentation/blob/7e97c511e0ba7e0de21e4cd5ea41b25d146d63d9/docs/symfony/DoctrineMigrations.md#doctrine-migrations-upgrade-from-2x-to-3x>)

### Build

```sh
composer install --no-dev -o
bin/console doctrine:migrations:migrate
```

See
[https://ckeditor.com/latest/samples/toolbarconfigurator/index.html#advanced](https://ckeditor.com/latest/samples/toolbarconfigurator/index.html#advanced)
for editor tools.

## Development

See [docs/development.md](docs/development.md) for information on setting up for
development.

## Release procedure

Make sure that the right repository is used for the core bundle, and that the
right version of the bundle is used:

```sh
composer config repositories.kontrolgruppen/core-bundle vcs https://github.com/aakb/kontrolgruppen-core-bundle
composer require 'kontrolgruppen/core-bundle:«some branch name»
```

Build production assets. They will be added to public/prod.

```sh
docker-compose run node yarn install
docker-compose run node yarn build
```

Commit the built files to git.

Tag the release.

## SAML

Create key and certificate (change `--subj` to match your actual setup):

```sh
mkdir -p saml/{idp,sp}
openssl req -x509 -sha256 -nodes -days 1460 -newkey rsa:2048 -keyout saml/sp/sp.key -out saml/sp/sp.crt \
    -subj "/C=DK/L=Aarhus/O=Kontrolgruppen/CN=kontrolgruppen.example.com/emailAddress=info@kontrolgruppen.example.com"
```

Download metadata from your identity provider (IdP) to `saml/idp/idp.xml`.

The actual locations of the key, certificate and IdP configuration files are
controlled by three environment variables:

```yaml
env(SAML_SP_CRT_FILE): '%kernel.project_dir%/saml/sp/sp.crt'
env(SAML_SP_KEY_FILE): '%kernel.project_dir%/saml/sp/sp.key'
env(SAML_IDP_CONFIG_FILE): '%kernel.project_dir%/saml/idp/idp.xml'
```

To change these, edit `.env.«env».local`, e.g.:

```sh
SAML_IDP_CONFIG_FILE='%kernel.project_dir%/saml/idp/my_idp.xml'
```

## BI exports

Run BI exports at regular intervals using `cron` or similar tools, e.g in `crontab`:

```sh
0 2 1 * * bin/console kontrolgruppen:report:export export@example.com 'Kontrolgruppen\CoreBundle\Export\BI\Export' --save
```

## Removal of expired completed Processes

Run the removal command at regular intervals using `cron` or similar tools, e.g
in `crontab`:

```sh
# Run the command every day at midnight
0 0 * * * bin/console kontrolgruppen:process:delete-completed-since
```
