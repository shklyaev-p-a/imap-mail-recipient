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
    /**@var String $server */
    protected $server;

    /** @var $resource */
    public $resource;

    /**
     * Client constructor.
     * @param String $email
     * @param String $password
     * @param String $server
     */
    public function __construct(String $email, String $password, $server = '')
    {
        $this->email = $email;
        $this->password = $password;
        $this->server = $server;
    }

    /**
     * @throws \Exception
     */
    public function connect(): void
    {
        $server = ($this->server) ? $this->server : AdressParser::getMailBox($this->email);
        $this->resource = @imap_open($server, $this->email, $this->password);

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
