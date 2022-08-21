<?php

namespace ImapRecipient\Constants;

class DomainsList
{
    const MAIL_RU = 'mail.ru';
    const GOOGLE_COM = 'google.com';
    const YANDEX_RU = 'yandex.ru';

    const DOMAINS = [
        self::MAIL_RU => '{imap.mail.ru:993/imap/ssl}',
        self::GOOGLE_COM => '{imap.gmail.com:993/imap/ssl}',
        self::YANDEX_RU => '{imap.yandex.ru:993/imap/ssl}'
    ];
}
