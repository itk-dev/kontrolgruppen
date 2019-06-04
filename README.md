# Kontrolgruppen

## Install

### Check out submodules
````sh
git submodule update --init --recursive
````

### Build
```sh
composer install --no-dev -o
bin/console doctrine:migrations:migrate
```

### Install CKEditor

```sh
php bin/console ckeditor:install
```

See `https://ckeditor.com/latest/samples/toolbarconfigurator/index.html#advanced` for editor tools.

## Development

See [README-develop.md](README-develop.md) for information on setting up for development.
