<?php

namespace ImapRecipient;

/**
 * Class Mails
 * @package ImapRecipient
 */
class Mails
{
    /** @var string $filters */
    protected $filters;
    /** @var $resource */
    protected $resource;
    /** @var int $flags */
    protected $flags;
    /** @var string $charset */
    protected $charset;

    /**
     * Mails constructor.
     * @param MailsBuilder $builder
     * @param $resource
     * @param int $flags
     * @param string $charset
     */
    public function __construct(MailsBuilder $builder, $resource, $flags = SE_FREE, $charset = "")
    {
        $this->filters = $builder->filters;
        $this->resource = $resource;
        $this->flags = $flags;
        $this->charset = $charset;
    }

    /**
     * @return array
     */
    public function mails(): array
    {
        $mails = imap_search($this->resource, $this->filters, $this->flags, $this->charset);
        return $mails ? $mails : [];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $mailsCount = imap_search($this->resource, $this->filters, $this->flags, $this->charset);
        return $mailsCount ? count($mailsCount) : 0;
    }
}