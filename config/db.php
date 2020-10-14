<?php

//parsing xml file
$xmlfile = file_get_contents(__DIR__ . '/web.xml');
$xmlJson = json_encode(simplexml_load_string($xmlfile));
$config = json_decode($xmlJson, true);



return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'],
    'username' => $config['user'],
    'password' => $config['password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
