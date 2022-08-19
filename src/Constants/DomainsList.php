<?php

namespace ImapRecipient\Constants;

class DomainsList{
    const DOMAINS = [
        'mail.ru' => '{imap.mail.ru:993/imap/ssl}',
        'google.com' => '{imap.gmail.com:993/imap/ssl}',
        'yandex.ru' => '{imap.yandex.ru:993/imap/ssl}'
    ];
}
