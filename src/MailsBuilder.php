<?php

namespace ImapRecipient;

class MailsBuilder
{
    /**
     *
     */
    const ALL = 'ALL';
    /**
     *
     */
    const ANSWERED = 'ANSWERED';
    /**
     *
     */
    const BCC = 'BCC';
    /**
     *
     */
    const BEFORE = 'BEFORE';
    /**
     *
     */
    const BODY = 'BODY';
    /**
     *
     */
    const CC = 'CC';
    /**
     *
     */
    const DELETED = 'DELETED';
    /**
     *
     */
    const FLAGGED = 'FLAGGED';
    /**
     *
     */
    const FROM = 'FROM';
    /**
     *
     */
    const KEYWORD = 'KEYWORD';
    /**
     *
     */
    const NEWEST = 'NEW';
    /**
     *
     */
    const OLD = 'OLD';
    /**
     *
     */
    const ON = 'ON';
    /**
     *
     */
    const RECENT = 'RECENT';
    /**
     *
     */
    const SEEN = 'SEEN';
    /**
     *
     */
    const SINCE = 'SINCE';
    /**
     *
     */
    const SUBJECT = 'SUBJECT';
    /**
     *
     */
    const TEXT = 'TEXT';
    /**
     *
     */
    const TO = 'TO';
    /**
     *
     */
    const UNANSWERED = 'UNANSWERED';
    /**
     *
     */
    const UNDELETED = 'UNDELETED';
    /**
     *
     */
    const UNFLAGGED = 'UNFLAGGED';
    /**
     *
     */
    const UNSEEN = 'UNSEEN';
    /**
     *
     */
    const UNKEYWORD = 'UNKEYWORD';

    /** @var $resource */
    public $resource;

    /** @var string $filters */
    public $filters = '';
    /** @var int $flags */
    protected $flags;
    /** @var string $charset */
    protected $charset;

    /**
     * MailsBuilder constructor.
     * @param $resource
     * @param int $flags
     * @param string $charset
     */
    public function __construct($resource, int $flags, string $charset)
    {
        $this->resource = $resource;
        $this->flags = $flags;
        $this->charset = $charset;
    }

    /**
     * @param string $text
     * @return MailsBuilder
     */
    public function unkeyword(string $text): MailsBuilder
    {
        $this->addFilter(self::UNKEYWORD, $text);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function undeleted(): MailsBuilder
    {
        $this->addFilter(self::UNDELETED);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function unflagged(): MailsBuilder
    {
        $this->addFilter(self::UNFLAGGED);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function unseed(): MailsBuilder
    {
        $this->addFilter(self::UNSEEN);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function unanswered(): MailsBuilder
    {
        $this->addFilter(self::UNANSWERED);
        return $this;
    }

    /**
     * @param string $text
     * @return MailsBuilder
     */
    public function text(string $text): MailsBuilder
    {
        $this->addFilter(self::TEXT, $text);
        return $this;
    }

    /**
     * @param string $recipient
     * @return MailsBuilder
     */
    public function to(string $recipient): MailsBuilder
    {
        $this->addFilter(self::TO, $recipient);
        return $this;
    }

    /**
     * @param string $subject
     * @return MailsBuilder
     */
    public function subject(string $subject): MailsBuilder
    {
        $this->addFilter(self::SUBJECT, $subject);
        return $this;
    }

    /**
     * @param string $date
     * @return MailsBuilder
     */
    public function since(string $date): MailsBuilder
    {
        $this->addFilter(self::SINCE, $date);
        return $this;
    }

    /**
     * @param string $date
     * @return MailsBuilder
     */
    public function on(string $date): MailsBuilder
    {
        $this->addFilter(self::ON, $date);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function recent(): MailsBuilder
    {
        $this->addFilter(self::RECENT);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function seen(): MailsBuilder
    {
        $this->addFilter(self::SEEN);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function newest(): MailsBuilder
    {
        $this->addFilter(self::NEWEST);
        return $this;
    }

    /**
     * @param string $from
     * @return MailsBuilder
     */
    public function from(string $from): MailsBuilder
    {
        $this->addFilter(self::FROM, $from);
        return $this;
    }

    /**
     * @param string $keyword
     * @return MailsBuilder
     */
    public function keyword(string $keyword): MailsBuilder
    {
        $this->addFilter(self::KEYWORD, $keyword);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function flagged(): MailsBuilder
    {
        $this->addFilter(self::FLAGGED);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function deleted(): MailsBuilder
    {
        $this->addFilter(self::DELETED);
        return $this;
    }

    /**
     * @param string $recipient Скрытые получатели письма
     *
     * @return MailsBuilder
     */
    public function bcc(string $recipient): MailsBuilder
    {
        //Add validator
        $this->addFilter(self::BCC, $recipient);
        return $this;
    }

    /**
     * @param string $recipient
     * @return MailsBuilder
     */
    public function cc(string $recipient): MailsBuilder
    {
        $this->addFilter(self::CC, $recipient);
        return $this;
    }

    /**
     * @param string $text
     * @return MailsBuilder
     */
    public function body(string $text): MailsBuilder
    {
        $this->addFilter(self::BODY, $text);
        return $this;
    }

    /**
     * @param string $date
     * @return MailsBuilder
     */
    public function before(string $date): MailsBuilder
    {
        $this->addFilter(self::BEFORE, $date);
        return $this;
    }

    /**
     * @return MailsBuilder
     */
    public function answered(): MailsBuilder
    {
        $this->addFilter(self::ANSWERED);
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->addFilter(self::ALL);
        $this->filters = trim($this->filters);
        $mails = (new Mails($this, $this->resource, $this->flags, $this->charset))->mails();
        return $mails ? $mails : [];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->addFilter(self::ALL);
        $this->filters = trim($this->filters);
        return (int)(new Mails($this, $this->resource, $this->flags, $this->charset))->count();
    }

    /**
     * @param string $filter
     * @param string $value
     */
    protected function addFilter(string $filter, $value = ''): void
    {
        $this->filters .= " {$filter}";

        if ($value) {
            $this->filters .= " \"{$value}\"";
        }
    }
}