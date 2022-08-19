<?php

namespace ImapRecipient\Helpers;

class MailStructure
{
    const FILENAME = 'filename';
    const NAME = 'name';

    public static function getSubjectDecode(string $subject)
    {
        $text = explode('?', $subject)[3];
        $encoding = explode('?', $subject)[2];

        switch ($encoding) {
            case 'B':
                return base64_decode($text);
                exit;
            case 'Q':
                return imap_8bit($text);
                exit;
            default:
                return $subject;
                exit;
        }
    }

    public static function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true)
    {

        foreach ($messageParts as $part) {
            $flattenedParts[$prefix . $index] = $part;
            if (isset($part->parts)) {
                if ($part->type == 2) {
                    $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix . $index . '.', 0, false);
                } elseif ($fullPrefix) {
                    $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix . $index . '.');
                } else {
                    $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix);
                }
                unset($flattenedParts[$prefix . $index]->parts);
            }
            $index++;
        }

        return $flattenedParts;
    }

    public static function getFilenameFromPart($part)
    {
        $filename = '';

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) == self::FILENAME) {
                    $filename = $object->value;
                }
            }
        }

        if (!$filename && $part->ifparameters) {
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) == self::NAME) {
                    $filename = $object->value;
                }
            }
        }

        return $filename;
    }
}