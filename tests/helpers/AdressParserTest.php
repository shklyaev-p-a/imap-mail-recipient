<?php

use PHPUnit\Framework\TestCase;
use ImapRecipient\Helpers\AdressParser;

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
    public function testGetUserName(string $email, string $userName): void
    {
        $this->assertEquals($userName, AdressParser::getUserName($email));
    }

    /**
     * @dataProvider emailsSubDomainProvider
     *
     * @param string $email
     * @param string $subDomain
     */
    public function testGetSubDomain(string $email, string $subDomain): void
    {
        $this->assertEquals($subDomain, AdressParser::getSubDomain($email));
    }

    /**
     * @throws ReflectionException
     */
    public function testGetMainDomainFromServerInfo(): void
    {
        $postServerData = [
            [
                "host" => "inbox.ru",
                "class" => "IN",
                "ttl" => 836,
                "type" => "MX",
                "pri" => 10,
                "target" => "mxs.mail.ru"
            ]
        ];

        $class = new ReflectionClass(AdressParser::class);
        $method = $class->getMethod('getMainDomainFromServerInfo');
        $method->setAccessible(true);

        $result = $method->invoke(new AdressParser(), $postServerData);
        $this->assertEquals('mail.ru', $result);
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
}