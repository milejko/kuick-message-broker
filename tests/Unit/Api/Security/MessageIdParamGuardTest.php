<?php

namespace Tests\KuickMessageBroker\Unit\Api\Security;

use Kuick\Http\BadRequestException;
use KuickMessageBroker\Api\Security\MessageIdParamGuard;
use Nyholm\Psr7\ServerRequest;

use function PHPUnit\Framework\assertEmpty;

class MessageIdParamGuardTest extends \PHPUnit\Framework\TestCase
{
    public function testIfMissingQueryParamGivesBadRequest(): void
    {
        $serverRequest = new ServerRequest('GET', 'not-important');
        $this->expectException(BadRequestException::class);
        (new MessageIdParamGuard())($serverRequest);
    }

    public function testIfMessageIdQueryValidatesProperly(): void
    {
        $serverRequest = new ServerRequest('GET', 'not-important?messageId=123');
        assertEmpty((new MessageIdParamGuard())($serverRequest));
    }
}
