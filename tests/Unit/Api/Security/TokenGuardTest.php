<?php

namespace Tests\KuickMessageBroker\Unit\Api\Security;

use KuickMessageBroker\Api\Security\TokenGuard;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;

class TokenGuardTest extends \PHPUnit\Framework\TestCase
{
    private const CONSUMERS = 'channel[]=user1&channel[]=user2&channel2[]=user3';
    private const PUBLISHERS = 'channel[]=puser1&channel[]=puser2&channel2[]=puser3';

    private static function createTokenGuard(): TokenGuard
    {
        return new TokenGuard(
            self::PUBLISHERS,
            self::CONSUMERS,
            new NullLogger()
        );
    }

    public function testIfMissingTokenGivesUnauthorized(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = new ServerRequest('GET', 'not-important?channel=test');
        $response = $tg($serverRequest);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testIfInvalidTokenGivesForbidden(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important?channel=channel'))
            ->withAddedHeader('Authorization', 'invalid');
        $response = $tg($serverRequest);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testIfNoTokensForChannelGivesForbidden(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important?channel=inexistent'))
            ->withAddedHeader('Authorization', 'Bearer some@user');
        $response = $tg($serverRequest);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testIfValidTokenPassesWithoutErrors(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important?channel=channel'))
            ->withAddedHeader('Authorization', 'Bearer user1');
        $this->assertNull($tg($serverRequest));
    }

    public function testIfValidTokenPassesWithoutErrorsForPost(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('POST', 'not-important?channel=channel'))
            ->withAddedHeader('Authorization', 'Bearer puser1');
        $this->assertNull($tg($serverRequest));
    }
}
