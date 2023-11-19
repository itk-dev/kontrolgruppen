<!-- markdownlint-disable MD024 -->

# Release Notes

All upgrade notes for this project will be documented in this file.

## [Unreleased]

## [3.1.0] - 2023-11-20

Nothing has been changed

## [3.0.0] - 2023-11-08

- Run migrations
- Run composer install
- Create a `certs` folder in the root of the project and add Datafordeler certificates in the folder (ask @ahma0942 for certificates)
- The following environment keys has been introduced (ask @ahma0942 for URLs & Public key passwords):

  ```
  DATAFORDELER_CPR_URL=''
  DATAFORDELER_CPR_PRIVATE_KEY_FILE=''
  DATAFORDELER_CPR_PUBLIC_KEY_FILE=''
  DATAFORDELER_CPR_PUBLIC_KEY_PASSWORD=''

  DATAFORDELER_CVR_URL=''
  DATAFORDELER_CVR_PRIVATE_KEY_FILE=''
  DATAFORDELER_CVR_PUBLIC_KEY_FILE=''
  DATAFORDELER_CVR_PUBLIC_KEY_PASSWORD=''

  DEFAULT_URI=''
  ```
