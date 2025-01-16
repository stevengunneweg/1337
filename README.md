# 1337online

A competition game for the 13:37 time.

Click the button on exactly 13:37 before everyone else. Please, no cheating with bots...

## Prerequisites

- npm
- php

## Project Setup

Run the following commands

```sh
npm install
npm run env:local
npm run server
```

And visit http://127.0.0.1:1234/api/init-db.php to set up the development database

## Development

```sh
npm run start
npm run server
```

The API is included in this project. You can run the API locally by running `npm run server`. This starts the PHP server which will communicate with the local sqlite database.

### Build

```sh
npm run build
OR
npm run build:<environment>
```

In order to build the application, make sure you have a `db.php` file in the parent folder of the repository.
The prod `db.php` file lives outside this repository for security.

## Contribution

Feel free to create a pull request or open an issue with your feature request.
