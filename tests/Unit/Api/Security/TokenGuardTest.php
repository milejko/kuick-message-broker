<?php

namespace Tests\KuickMessageBroker\Unit\Api\Security;

use Kuick\Http\BadRequestException;
use Kuick\Http\ForbiddenException;
use Kuick\Http\UnauthorizedException;
use KuickMessageBroker\Api\Security\TokenGuard;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertTrue;

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
        $serverRequest = new ServerRequest('GET', 'not-important');
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Token is missing');
        $tg('test', $serverRequest);
    }

    public function testIfInvalidTokenGivesForbidden(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important'))
            ->withAddedHeader('Authorization', 'invalid');
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Token is invalid');
        $tg('channel', $serverRequest);
        $serverRequest = (new ServerRequest('POST', 'not-important'))
            ->withAddedHeader('Authorization', 'invalid');
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Token is invalid');
        $tg('channel', $serverRequest);
    }

    public function testIfNoTokensForChannelGivesForbidden(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important'))
            ->withAddedHeader('Authorization', 'Bearer some@user');
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('No tokens found for this channel: inexistent');
        $tg('inexistent', $serverRequest);
    }

    public function testIfValidTokenPassesWithoutErrors(): void
    {
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('GET', 'not-important'))
            ->withAddedHeader('Authorization', 'Bearer user1');
        $tg('channel', $serverRequest);
        $tg = self::createTokenGuard();
        $serverRequest = (new ServerRequest('POST', 'not-important'))
            ->withAddedHeader('Authorization', 'Bearer puser3');
        $tg('channel2', $serverRequest);
        assertTrue(true);
    }
}
