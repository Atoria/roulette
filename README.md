Configuration:

database: from `web.dist.xml` create new file `web.xml` and populate with database credentials

socket: from `params.dist.php` create new file `params.php` and set socket domain, in config create `config.js` from `config.dist.js` file

In socket folder run `npm install`

Project setup:

Run: composer install

Running migrations: `php yii migrate`

Run node server: `node socket/server.js`