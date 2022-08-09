<?php

namespace ImapRecipient;

use ImapRecipient\Helpers\AdressParser;

/**
 * Class Client
 * @package ImapRecipient
 */
class Client
{
    /** @var String $email */
    protected $email;
    /** @var String $password */
    protected $password;

    /** @var $resource */
    public $resource;

    /**
     * Client constructor.
     * @param String $email
     * @param String $password
     */
    public function __construct(String $email, String $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @throws \Exception
     */
    public function connect(): void
    {
        $this->resource = @imap_open(AdressParser::getMailBox($this->email), $this->email, $this->password);

        if ($this->resource === false) {
            throw new \Exception(imap_last_error());
        }
    }

    /**
     * @param int $flags
     * @param string $charset
     * @return MailsBuilder
     */
    public function all($flags = SE_FREE, $charset = ''): MailsBuilder
    {
        return new MailsBuilder($this->resource, $flags, $charset);
    }

    /**
     * @param int $number
     * @return Mail
     */
    public function getOne(int $number): Mail
    {
        return new Mail($this->resource, $number);
    }

    /**
     *
     */
    public function close(): void
    {
        imap_close($this->resource);
    }
}
