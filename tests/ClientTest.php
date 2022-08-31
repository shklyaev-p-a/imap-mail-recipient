<?php

use ImapRecipient\MailsBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 */
final class ClientTest extends TestCase
{
    /**
     * Checking call Builder class and return this
     */
    public function testAll():void
    {
        $builder = $this->createMock(MailsBuilder::class);

        $dependencyResolver = $this->createMock(\ImapRecipient\Client::class);
        $dependencyResolver->expects($this->once())
            ->method('all')
            ->willReturn($builder);

        $this->assertSame($builder, $dependencyResolver->all());
    }

    /**
     * Checking call Mail class and return this
     */
    public function testGetOne():void
    {
        $mail = $this->createMock(\ImapRecipient\Mail::class);

        $dependencyResolver = $this->createMock(\ImapRecipient\Client::class);
        $dependencyResolver->expects($this->once())
            ->method('getOne')
            ->with(1)
            ->willReturn($mail);

        $this->assertSame($mail, $dependencyResolver->getOne(1));
    }
}