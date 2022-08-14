#Connect to mailbox

`<?php`

`require('vendor/autoload.php');`

`use ImapRecipient\Client;`

`$client = new Client('email', 'password');`
<br />
`$client->connect();`

At this moment available connection to yandex, mail and google mailbox domains. for domains extensions need add new domains for Constants\DomainsList
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


____


#Get one mails with attachments

`$client->getOne(int $number);`

`$client->getOne(int $nubmer)->attachments;'`
