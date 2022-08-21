<?php

use PHPUnit\Framework\TestCase;
use ImapRecipient\Helpers\AdressParser;
use ImapRecipient\Constants\DomainsList;

/**
 * Class AdressParserTest
 */
final class AdressParserTest extends TestCase
{
    /**
     * @dataProvider emailsNameProvider
     *
     * @param string $email
     * @param string $userName
     */
    public function testGetUserName(string $email, string $userName)
    {
        $this->assertEquals($userName, AdressParser::getUserName($email));
    }

    /**
     * @dataProvider emailsSubDomainProvider
     *
     * @param string $email
     * @param string $subDomain
     */
    public function testGetSubDomain(string $email, string $subDomain)
    {
        $this->assertEquals($subDomain, AdressParser::getSubDomain($email));
    }

    /**
     * @dataProvider emailsMailBoxProvider
     *
     * @param string $email
     * @param string $domain
     * @param string $mailbox
     *
     */
    public function testGetMailbox(string $email, string $domain, string $mailbox)
    {
        $adressParserClass = $this->createPartialMock(AdressParser::class, ['getDomain']);
        $adressParserClass->method('getDomain')->willReturnSelf()->willReturn($domain);

        $this->assertEquals($mailbox, $adressParserClass::getMailBox($email));
    }

    public function testGetDomain()
    {
        $postServerData = [
            "host" => "inbox.ru",
            "class" => "IN",
            "ttl" => 836,
            "type" => "MX",
            "pri" => 10,
            "target" => "mxs.mail.ru"
        ];
        $adressParserClass = $this->createPartialMock(AdressParser::class, ['getDnsRecord']);
        $adressParserClass->method('getDnsRecord')->willReturn($postServerData);

        $this->assertEquals('mail.ru', $adressParserClass::getDomain('inbox.ru'));
    }

    /**
     * @return array
     */
    public function emailsNameProvider(): array
    {
        return [
            ['no_problem@test.ru', 'no_problem'],
            ['test123.123@test.ru', 'test123.123'],
            ['firstname-lastname@test.ru', 'firstname-lastname']
        ];
    }

    /**
     * @return array
     */
    public function emailsSubDomainProvider(): array
    {
        return [
            ['no_problem@test.ru', 'test.ru'],
            ['test123.123@mail.ru', 'mail.ru'],
            ['firstname-lastname@gmail.com', 'gmail.com']
        ];
    }

    /**
     * @return array
     */
    public function emailsMailBoxProvider(): array
    {
        return [
            ['test@internet.ru', DomainsList::MAIL_RU, DomainsList::DOMAINS[DomainsList::MAIL_RU]],
            ['test@inbox.ru', DomainsList::MAIL_RU, DomainsList::DOMAINS[DomainsList::MAIL_RU]],
            ['test@list.ru', DomainsList::MAIL_RU, DomainsList::DOMAINS[DomainsList::MAIL_RU]],
            ['test@gmail.com', DomainsList::GOOGLE_COM, DomainsList::DOMAINS[DomainsList::GOOGLE_COM]],
            ['test@yandex.ru', DomainsList::YANDEX_RU, DomainsList::DOMAINS[DomainsList::YANDEX_RU]]
        ];
    }
}