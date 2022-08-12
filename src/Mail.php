<?php

namespace ImapRecipient;

use ImapRecipient\Constants\MediaList;
use ImapRecipient\Media\Image;
use ImapRecipient\Media\MediaInterface;
use ImapRecipient\Traits\PartTrait;

class Mail
{
    use PartTrait;

    protected $number;
    protected $resource;

    public $bodyPart;
    public $attachments;

    public $from = '';
    public $name = '';
    public $subject = '';

    public function __construct($resource, $number)
    {
        $this->number = $number;
        $this->resource = $resource;
        $this->setParts();
        $this->setHeaderFromAndSubject();
    }

    public function setHeaderFromAndSubject()
    {
        $header = imap_header($this->resource, $this->number);
        $this->from = trim($header->from[0]->mailbox . $header->from[0]->host);
        $this->name = trim($header->from[0]->personal);
        $this->subject = imap_utf8($header->subject);
    }

    public function from(): string
    {
        return $this->from;
    }

    public function images(): array
    {
        return $this->attachments['images'];
    }

    public function attachments(): MediaInterface
    {
        return MediaInterface::class;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function header(): string
    {
        return imap_fetchheader($this->resource, $this->number);
    }

    public function body(): string
    {
        return $this->getPartDecode($this->resource, $this->number, $this->bodyPart['partNumber'], $this->bodyPart['encoding']);
    }

    protected function setParts()
    {
        $structure = imap_fetchstructure($this->resource, $this->number);
        $flattenedParts = $this->flattenParts($structure->parts);
        foreach ($flattenedParts as $partNumber => $part) {
            switch ($part->type) {
                case TYPETEXT:
                    $this->bodyPart = [
                        'partNumber' => $partNumber,
                        'encoding' => $part->encoding
                    ];
                    break;
                case TYPEMULTIPART:
                    // multi-part headers, can ignore
                    break;
                case TYPEMESSAGE:
                    // attached message headers, can ignore
                    break;
                case TYPEAPPLICATION: // application
                    $this->addAttachment($part, $partNumber, MediaList::FILES);
                    break;
                case TYPEAUDIO:
                    $this->addAttachment($part, $partNumber, MediaList::AUDIOS);
                    break;
                case TYPEIMAGE:
                    $this->addAttachment($part, $partNumber, MediaList::IMAGES);
                    break;
                case TYPEVIDEO:
                    $this->addAttachment($part, $partNumber, MediaList::VIDEOS);
                    break;
                case TYPEOTHER:
                    $this->addAttachment($part, $partNumber, MediaList::OTHER);
                    break;
            }
        }
    }

    protected function addAttachment($part, $partNumber, $name)
    {
        $filename = $this->getFilenameFromPart($part);
        if ($filename) {
            if ($name === MediaList::IMAGES) {
                $this->attachments[$name][] = new Image($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
            } else {
                $this->attachments[$name] = [
                    'name' => $filename,
                    'format' => $part->subtype,
                    'part' => $partNumber,
                    'encoding' => $part->encoding
                ];
            }
        }
    }

    protected function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true)
    {

        foreach ($messageParts as $part) {
            $flattenedParts[$prefix . $index] = $part;
            if (isset($part->parts)) {
                if ($part->type == 2) {
                    $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix . $index . '.', 0, false);
                } elseif ($fullPrefix) {
                    $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix . $index . '.');
                } else {
                    $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix);
                }
                unset($flattenedParts[$prefix . $index]->parts);
            }
            $index++;
        }

        return $flattenedParts;

    }

    protected function getFilenameFromPart($part)
    {
        $filename = '';

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) == 'filename') {
                    $filename = $object->value;
                }
            }
        }

        if (!$filename && $part->ifparameters) {
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) == 'name') {
                    $filename = $object->value;
                }
            }
        }

        return $filename;

    }
}