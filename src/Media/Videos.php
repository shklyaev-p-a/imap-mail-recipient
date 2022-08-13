<?php

namespace ImapRecipient\Media;

use ImapRecipient\Traits\PartTrait;

class Videos implements MediaInterface
{
    use PartTrait;

    protected $name;
    protected $part;
    protected $encoding;
    protected $format;
    protected $resource;
    protected $number;

    public function __construct($resource, $number, $name, $part, $encoding, $format)
    {
        $this->resource = $resource;
        $this->number = $number;
        $this->name = $name;
        $this->part = $part;
        $this->encoding = $encoding;
        $this->format = $format;
    }

    public function get()
    {
        return $this->getPartDecode($this->resource, $this->number, $this->part, $this->encoding);
    }
}