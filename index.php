<?php

require('vendor/autoload.php');

use ImapRecipient\Client;

//Example
$client = new Client('local.test.123@mail.ru', 'Mr7gCnXVcTbqQWxcQ1UG');
$client->connect();
echo "<pre>";
var_dump($client->getOne(1));
echo "</pre>";
$client->close();