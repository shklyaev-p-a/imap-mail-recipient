<?php

namespace ImapRecipient\Helpers;

/**
 * Class Decoder
 * @package ImapRecipient
 */
class Decoder
{
    /**
     * @param $text
     * @param int $encoding
     * @return string
     */
    public static function decode($text, $encoding = ENCBASE64): string
    {
        switch ($encoding) {
            case ENC7BIT:
                return $text;
            case ENC8BIT:
                return quoted_printable_decode(imap_8bit($text));
            case ENCBINARY:
                return imap_binary($text);
            case ENCBASE64:
                return imap_base64($text);
            case ENCQUOTEDPRINTABLE:
                return quoted_printable_decode($text);
            case ENCOTHER:
                return $text;
            default:
                return $text;
        }
    }
}