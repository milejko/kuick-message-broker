<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\MessageBroker\Store;

use Kuick\MessageBroker\Infrastructure\ObjectNameValidator;

/**
 * Disk backed publisher
 */
class ObjectNameValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testIfCorrectNamesAreValidatedCorrectly(): void
    {
        $v = new ObjectNameValidator();
        self::assertTrue($v->isValid('sample'));
        self::assertTrue($v->isValid('some-name'));
        self::assertTrue($v->isValid('dot.separated.name'));
        self::assertTrue($v->isValid('some-other-name'));
        self::assertTrue($v->isValid('old_style_other_name'));
        self::assertTrue($v->isValid('pretty_long_old_style-mixed-with-new-one'));
        self::assertTrue($v->isValid('why-not-some-digits-123456789'));
        self::assertTrue($v->isValid('_strange_but_why.not-666'));
        self::assertTrue($v->isValid('john@travolta.com'));
        self::assertTrue($v->isValid('admin@xUjJhsf5ty7OpLL'));
    }

    public function testIfJunkIsNotPassedByTheValidator(): void
    {
        $v = new ObjectNameValidator();
        self::assertFalse($v->isValid('x')); //to short
        self::assertFalse($v->isValid('invalid-characters-?'));
        self::assertFalse($v->isValid('invalid-characters-$'));
        self::assertFalse($v->isValid('invalid-characters-,'));
        self::assertFalse($v->isValid('invalid-characters-#'));
        self::assertFalse($v->isValid('invalid-characters-!'));
        self::assertFalse($v->isValid('invalid-characters-^'));
        self::assertFalse($v->isValid('invalid-characters-('));
        self::assertFalse($v->isValid('invalid-characters-)'));
        self::assertFalse($v->isValid('invalid-characters-+'));
        self::assertFalse($v->isValid('loooooooooooooooooooooooooooooooooooooong'));
    }
}
