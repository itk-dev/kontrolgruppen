# Kontrolgruppen

This project consists of two parts: The application (in the `src/` directory) and
[the core bundle](#the-core-bundle) (in the `core/` directory).

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

Build production assets. They will be put in the `public/prod` directory.

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

## The core bundle

### Environment

Set the site name in the .env file:

```ini
SITE_NAME=your-site-name
```

### SAML (core)

Configuration of SAML login must be done with configuration under `kontrolgruppen_core.saml`.

Example configuration:

```yaml
## @see https://symfony.com/doc/current/configuration/environment_variables.html#environment-variable-processors
parameters:
    env(ROUTER_REQUEST_CONTEXT_BASE_URL): ''
    router.request_context.scheme: '%env(ROUTER_REQUEST_CONTEXT_SCHEME)%'
    router.request_context.host: '%env(ROUTER_REQUEST_CONTEXT_HOST)%'
    router.request_context.base_url: '%env(ROUTER_REQUEST_CONTEXT_BASE_URL)%'
    base_url: '%router.request_context.scheme%://%router.request_context.host%%router.request_context.base_url%'

    env(SAML_SP_CRT_FILE): '%kernel.project_dir%/saml/sp/sp.crt'
    env(SAML_SP_KEY_FILE): '%kernel.project_dir%/saml/sp/sp.key'
    env(SAML_IDP_CONFIG_FILE): '%kernel.project_dir%/saml/idp/idp.xml'
    saml_sp_crt: '%env(file:resolve:SAML_SP_CRT_FILE)%'
    saml_sp_key: '%env(file:resolve:SAML_SP_KEY_FILE)%'
    saml_idp_config_file: '%env(resolve:SAML_IDP_CONFIG_FILE)%'

kontrolgruppen_core:
    …

    saml:

        php_saml_settings:
            # https://github.com/onelogin/php-saml#settings
            strict:              true
            debug:               true
            sp:
                entityId: '%base_url%'
                assertionConsumerService:
                    url: '%base_url%/saml/acs'
                    binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                singleLogoutService:
                    url: '%base_url%/saml/sls'
                    binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
                NameIDFormat: 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'
                x509cert: '%saml_sp_crt%'
                privateKey: '%saml_sp_key%'

            # Read IdP configuration from a file.
            idp: '%saml_idp_config_file%'

            # Advanced settings (https://github.com/onelogin/php-saml#settings)
            compress:
                requests: true
                responses: true
            security:
                nameIdEncrypted: false
                authnRequestsSigned: false
                logoutRequestSigned: false
                logoutResponseSigned: false
                signMetadata: false
                wantMessagesSigned: false
                wantAssertionsEncrypted: false
                wantAssertionsSigned: false
                wantNameId: true
                wantNameIdEncrypted: false
                requestedAuthnContext: true
                wantXMLValidation: true
                relaxDestinationValidation: false
                signatureAlgorithm: http://www.w3.org/2001/04/xmldsig-more#rsa-sha256
                digestAlgorithm: http://www.w3.org/2001/04/xmlenc#sha256
                lowercaseUrlencoding: false
            contactPerson:
                technical:
                    givenName: ITK Development
                    emailAddress: itk-dev@aarhus.dk
                support:
                    givenName: ITK Development
                    emailAddress: itk-dev@aarhus.dk
            organization:
                en-US:
                    name: Kontrolgruppen
                    displayname: Kontrolgruppen
                    url: https://kontrolgruppen.example.com

            user_roles:
                attribute: http://schemas.microsoft.com/ws/2008/06/identity/claims/role
                # Map from ADFS stuff to Symfony roles
                fields:
                    # Common Name
                    CN:
                        DG-Right-Kontrolsystem-Admin: ROLE_ADMIN
                        DG-Right-Kontrolsystem-Sagsbehandler: ROLE_SAGSBEHANDLER
```

To make this example configuration work, you must create a key and a certificate
(change `--subj` to match your actual setup):

```sh
mkdir -p saml/{idp,sp}
openssl req -x509 -sha256 -nodes -days 1460 -newkey rsa:2048 \
    -keyout saml/sp/sp.key \
    -out saml/sp/sp.crt \
    -subj "/C=DK/L=Aarhus/O=Kontrolgruppen/CN=kontrolgruppen.example.com/emailAddress=info@kontrolgruppen.example.com"
```

and download metadata from your identity provider (IdP) to `saml/idp/idp.xml`.

### Sign in from command line

Rather than signing in via SAML, you can get a sign in url from the command
line. Run

```sh
bin/console kontrolgruppen:user:login --help
```

for details.

### Business Intelligence™

Users with role `ROLE_BI` can access and download reports from `/bi/`. Reports
should be run regularly by using `cron` or similar means to execute the
`kontrolgruppen:report:export` cli command.

Run

```sh
bin/console kontrolgruppen:report:export --help
```

to see details on command parameters and available reports (defined in the
`kontrolgruppen_core.exports` configuration).

#### Example

```sh
bin/console kontrolgruppen:report:export admin@example.com \
    'Kontrolgruppen\CoreBundle\Export\KL\Export' \
    --parameters='startdate=-1 month enddate=now'
```

#### Protip

Use `--debug-parameters` to see what the parameter values actually are:

```sh
bin/console kontrolgruppen:report:export admin@example.com \
    'Kontrolgruppen\CoreBundle\Export\KL\Export' \
    --parameters='startdate=-1 month enddate=now' --debug-parameters
```

### CPR and CVR lookup services

The following environment variables must be set in the `.env.local` file:

```ini
AZURE_TENANT_ID='xyz'
AZURE_APPLICATION_ID='xyz'
AZURE_CLIENT_SECRET='xyz'
AZURE_KEY_VAULT_SECRET='xyz'
AZURE_KEY_VAULT_SECRET_VERSION='xyz'

## CPR lookup
SERVICEPLATFORMEN_CPR_SERVICE_AGREEMENT_UUID='xyz'
SERVICEPLATFORMEN_CPR_USER_SYSTEM_UUID='xyz'
SERVICEPLATFORMEN_CPR_USER_UUID='xyz'

SERVICEPLATFORMEN_CPR_SERVICE_CONTRACT='%kernel.project_dir%/vendor/itk-dev/serviceplatformen/resources/person-base-data-extended-service-contract/wsdl/context/PersonBaseDataExtendedService.wsdl'
SERVICEPLATFORMEN_CPR_SERVICE_ENDPOINT='https://xyz.com'
SERVICEPLATFORMEN_CPR_SERVICE_UUID='xyz'

## CVR lookup
SERVICEPLATFORMEN_CVR_SERVICE_AGREEMENT_UUID='xyz'
SERVICEPLATFORMEN_CVR_USER_SYSTEM_UUID='xyz'
SERVICEPLATFORMEN_CVR_USER_UUID='xyz'

SERVICEPLATFORMEN_CVR_SERVICE_CONTRACT='%kernel.project_dir%/vendor/itk-dev/serviceplatformen/resources/online-service-contract/wsdl/context/OnlineService.wsdl'
SERVICEPLATFORMEN_CVR_SERVICE_ENDPOINT='https://xyz.com'
SERVICEPLATFORMEN_CVR_SERVICE_UUID='xyz'
```

#### Updating addresses on clients from the command line

There is a console command available for updating client addresses based on
information from the CPR service:

```bash
bin/console kontrolgruppen:client:update

## To perform a dry-run (without updating the client) append the --dry-run flag
bin/console kontrolgruppen:client:update --dry-run

## Show info about how many clients and which clients that are updated:
## This can be used with the --dry-run flag as well.
bin/console kontrolgruppen:client:update -vvv
```

##### Automatically update the client addresses

With cron you could for example run the console command every night at 02:00.
Add this to your crontab:

```cron
0 2 * * * /usr/bin/env php path/to/kontrolgruppen/bin/console kontrolgruppen:client:update
```
