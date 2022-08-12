<?php

require('vendor/autoload.php');

use ImapRecipient\Client;

//Example
$client = new Client('local.test.123@mail.ru', 'Mr7gCnXVcTbqQWxcQ1UG');
$client->connect();
echo "<pre>";
var_dump(imap_search($client->resource, 'SINCE "4 August 2022 19:45:13" BEFORE "14 August 2022 19:45:13"'));
echo "</pre>";
$client->close();