# Etek data migration

Hack to migrate data from ETEK.

## Usage

First, generate JSON data files into a directory, e.g. `_misc/legacy/data`. How
to actually do this is a closely guarded secret.

```shell script
bin/console etek:fixtures:generate _misc/legacy/data
bin/console hautelook:fixtures:load --bundle=EtekDataMigrationBundle
bin/console etek:fixtures:generate --unescape-content
```

To generate and import fixtures in one go, use

```shell script
bin/console etek:fixtures:generate _misc/legacy/data -vvv --no-interaction
```
