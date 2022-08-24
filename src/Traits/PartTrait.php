<?php

namespace ImapRecipient\Traits;

use ImapRecipient\Helpers\Decoder;

trait PartTrait
{
    protected function getPart($resource, int $number, $partNumber)
    {
        return imap_fetchbody($resource, $number, $partNumber);
    }

    protected function getPartDecode($resource, int $number, $partNumber, $encoding, $bodyEncoding = 'utf-8')
    {
        $data = imap_fetchbody($resource, $number, $partNumber);
        $data = trim(Decoder::decode($data, $encoding));
        if ($bodyEncoding !== 'utf-8') {
            $data = mb_convert_encoding(quoted_printable_decode($data), 'UTF-8', $bodyEncoding);
        }

        return $data;
    }
}

