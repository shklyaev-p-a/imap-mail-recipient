<?php

namespace ImapRecipient;

use ImapRecipient\Constants\MediaList;
use ImapRecipient\Helpers\MailStructure;
use ImapRecipient\Media\Audio;
use ImapRecipient\Media\File;
use ImapRecipient\Media\Image;
use ImapRecipient\Media\Other;
use ImapRecipient\Media\Video;
use ImapRecipient\Traits\PartTrait;

class Mail
{
    use PartTrait;

    const PART_NUMBER = 'partNumber';
    const ENCODING = 'encoding';
    const TYPE_PLAIN = 'PLAIN';
    const TYPE_HTML = 'HTML';
    const BODY_ENCODING = 'bodyEncoding';

    protected $number;
    protected $resource;

    public $htmlPart = [];
    public $textPart = [];
    public $attachments = [];

    public $from = '';
    public $name = '';
    public $subject = '';
    public $date = '';
    public $messageId = '';
    public $ownerId = '';
    public $isParent = false;

    public function __construct($resource, $number)
    {
        $this->number = $number;
        $this->resource = $resource;
        $this->parsePart();
        $this->setDataFromHeader();
    }

    public function setDataFromHeader()
    {
        $header = $this->header();
        $this->from = trim($header->from[0]->mailbox . '@' . $header->from[0]->host);
        $this->name = imap_utf8(trim($header->from[0]->personal));
        $this->subject = MailStructure::getSubjectDecode($header->subject ?? '');
        $this->date = $header->MailDate;
        $this->messageId = $header->message_id;
        $this->isParent = (bool)property_exists($header, 'references');
        $this->ownerId = (property_exists($header, 'references')) ? explode(' ', $header->references)[0] : '';
    }

    public function attachments(): array
    {
        return $this->attachments;
    }

    public function images(): array
    {
        return array_key_exists(MediaList::IMAGES, $this->attachments) ? $this->attachments[MediaList::IMAGES] : [];
    }

    public function files(): array
    {
        return array_key_exists(MediaList::FILES, $this->attachments) ? $this->attachments[MediaList::FILES] : [];
    }

    public function audios(): array
    {
        return array_key_exists(MediaList::AUDIOS, $this->attachments) ? $this->attachments[MediaList::AUDIOS] : [];
    }

    public function videos(): array
    {
        return array_key_exists(MediaList::VIDEOS, $this->attachments) ? $this->attachments[MediaList::VIDEOS] : [];
    }

    public function others(): array
    {
        return array_key_exists(MediaList::OTHERS, $this->attachments) ? $this->attachments[MediaList::OTHERS] : [];
    }

    public function from(): string
    {
        return $this->from;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function header(): object
    {
        return imap_headerinfo($this->resource, $this->number);
    }

    public function html(): ?string
    {
        return (!array_key_exists(self::PART_NUMBER, $this->htmlPart) || !array_key_exists(self::ENCODING, $this->htmlPart))
            ?
            ''
            :
            $this->getPartDecode($this->resource, $this->number, $this->htmlPart[self::PART_NUMBER], $this->htmlPart[self::ENCODING], $this->htmlPart[self::BODY_ENCODING]);
    }

    public function text(): ?string
    {
        return (!array_key_exists(self::PART_NUMBER, $this->textPart) || !array_key_exists(self::ENCODING, $this->textPart))
            ?
            ''
            :
            $this->getPartDecode($this->resource, $this->number, $this->textPart[self::PART_NUMBER], $this->textPart[self::ENCODING], $this->textPart[self::BODY_ENCODING]);
    }

    public function ownerId(): string
    {
        return $this->ownerId;
    }

    public function isParent(): bool
    {
        return $this->isParent;
    }

    public function messageId(): string
    {
        return $this->messageId;
    }

    /**
     * @param $structure root fetchstructure or part of parts in fetchstructure object
     * @param int $partNumber default 1 if not parts array in fetchstructure object
     *
     * @return void
     */
    protected function setParts($structure, $partNumber = 1): void
    {
        if ($structure->subtype === self::TYPE_PLAIN) {
            $this->textPart = [
                self::PART_NUMBER => $partNumber,
                self::ENCODING => $structure->encoding,
                self::BODY_ENCODING => $structure->parameters[0]->value
            ];
        }
        if ($structure->subtype === self::TYPE_HTML) {
            $this->htmlPart = [
                self::PART_NUMBER => $partNumber,
                self::ENCODING => $structure->encoding,
                self::BODY_ENCODING => $structure->parameters[0]->value
            ];
        }
    }

    protected function parsePart()
    {
        $structure = imap_fetchstructure($this->resource, $this->number);
        if (!property_exists($structure, 'parts')) {
            $this->setParts($structure);
            return true;
        }

        $flattenedParts = MailStructure::flattenParts($structure->parts);
        foreach ($flattenedParts as $partNumber => $part) {
            $filename = MailStructure::getFilenameFromPart($part);
            switch ($part->type) {
                case TYPETEXT:
                    $this->setParts($part, $partNumber);
                    break;
                case TYPEMULTIPART:
                    // multi-part headers, can ignore
                    break;
                case TYPEMESSAGE:
                    // attached message headers, can ignore
                    break;
                case TYPEAPPLICATION: // application
                    $this->attachments[MediaList::FILES][] = new File($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
                    break;
                case TYPEAUDIO:
                    $this->attachments[MediaList::AUDIOS][] = new Audio($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
                    break;
                case TYPEIMAGE:
                    $this->attachments[MediaList::IMAGES][] = new Image($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
                    break;
                case TYPEVIDEO:
                    $this->attachments[MediaList::VIDEOS][] = new Video($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
                    break;
                case TYPEOTHER:
                    $this->attachments[MediaList::OTHERS][] = new Other($this->resource, $this->number, $filename, $partNumber, $part->encoding, $part->subtype);
                    break;
            }
        }
    }
}