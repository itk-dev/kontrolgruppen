# Kontrolgruppen

## Install

Run

```
./bin/install --env=prod
```

to install in production.


### Check out submodules
````sh
git submodule update --init --recursive
````

### Build
```sh
composer install --no-dev -o
bin/console doctrine:migrations:migrate
```

See `https://ckeditor.com/latest/samples/toolbarconfigurator/index.html#advanced` for editor tools.

## Development

See [README-develop.md](README-develop.md) for information on setting up for development.

## Todo
Maybe we should use the composer-merge plugin from wikimedia, where we will have a composer-dev.json which overrides the
main composer.json. In this dev composer json the repo to the core-bundle package is defined as a path, but in the main
composer file the repo is defined as a vcs.

The composer merge plugin should be added under the require-dev section in the main composer file, so it will only
run when --no-dev is not present when running composer install.

This will replace the initializing and updating of submodules when deploying to production, because the required kontrolgruppen
package will be fetched from vcs instead.

## Release procedure.

Build production assets. They will be added to public/prod.

```sh
yarn build
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

The actual locations of the key, certificate and IdP configuration files are controlled by three environment variables:

```yaml
env(SAML_SP_CRT_FILE): '%kernel.project_dir%/saml/sp/sp.crt'
env(SAML_SP_KEY_FILE): '%kernel.project_dir%/saml/sp/sp.key'
env(SAML_IDP_CONFIG_FILE): '%kernel.project_dir%/saml/idp/idp.xml'
```

To change these, edit `.env.«env».local`, e.g.:

```sh
SAML_IDP_CONFIG_FILE='%kernel.project_dir%/saml/idp/my_idp.xml'
```
