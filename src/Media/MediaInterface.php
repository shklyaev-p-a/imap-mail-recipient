<?php

namespace ImapRecipient\Media;

interface MediaInterface
{
    public function get();
    public function format();
    public function name();
}