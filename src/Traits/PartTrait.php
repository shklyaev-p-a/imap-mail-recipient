<?php

namespace ImapRecipient\Traits;

use ImapRecipient\Helpers\Decoder;

trait PartTrait
{
    protected function getPart($resource, int $number, $partNumber)
    {
        return imap_fetchbody($resource, $number, $partNumber);
    }

    protected function getPartDecode($resource, int $number, $partNumber, $encoding)
    {
        $data = imap_fetchbody($resource, $number, $partNumber);
        return trim(Decoder::decode($data, $encoding));
    }
}