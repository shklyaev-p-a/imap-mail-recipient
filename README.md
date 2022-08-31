#About
It`s activity developing library for simple getting mail via php imap function.

**Attention**

_Methods and arguments constantly changing. The library is under development_

**Attention**

For joint development contact with me: shklyaev.p.a@mail.ru

#Connect to mailbox

`<?php`

`require('vendor/autoload.php');`

`use ImapRecipient\Client;`

`$client = new Client('email', 'password', 'server');`
<br />
`$client->connect();`

At this moment available connection to yandex, mail and google mailbox domains. For another server connection need passed to function server adress as third argument. Example: `'{imap.mail.ru:993/imap/ssl}'`
For check official servers domain use `\ImapRecipient\Helpers\AdressParser::getMailBox('email')` helper. But imap domain will can be find in google.


____

#Get all mails from mailbox
_**method will return array with mail number. For get mail you need use getOne() method**_

`$client->all()->get();`

for filtering mails before getting you may used special imap filters in main query.

Example:
<br/>
`$client->all()->answered()->body('Hello world')->get();`

full filtering list u can find on https://www.php.net/manual/ru/function.imap-search.php

All dates format in filters must be as `'j F Y H:m:s'`
____


#Get one mails with attachments

`$client->getOne(int $number);`

`$client->getOne(int $number)->text()` or  `$client->getOne(int $number)->html()` 

`$client->getOne(int $nubmer)->attachments;'` for looking attachments

`$images = $client->getOne(int $number)->images()[0]->get()` for get image content (similarly for another files implements MediaInterface)


test running: ` php ./vendor/bin/phpunit` or `composer test`