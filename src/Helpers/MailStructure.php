<?php

namespace ImapRecipient\Helpers;

class MailStructure
{
    const FILENAME = 'filename';
    const NAME = 'name';

    const BASE_64 = 'B';
    const IMAP_8BIT = 'Q';

    /**
     * @param string $subject
     * @return string
     */
    public static function getSubjectDecode(string $subject): string
    {
        $subjectArray = explode('?', $subject);

        if(count($subjectArray) === 1) return $subject;

        $text = $subjectArray[3];
        $encoding = $subjectArray[2];

        //toDo:: move to Decoder helper
        switch ($encoding) {
            case self::BASE_64:
                return base64_decode($text);
                exit;
            case self::IMAP_8BIT:
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

    /**
     * @param \stdClass $part
     * @return string
     */
    public static function getFilenameFromPart(\stdClass $part): string
    {
        $filename = '';

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) === self::FILENAME) {
                    $filename = $object->value;
                }
            }
        }

        if (!$filename && $part->ifparameters) {
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) === self::NAME) {
                    $filename = $object->value;
                }
            }
        }

        return $filename;
    }
}