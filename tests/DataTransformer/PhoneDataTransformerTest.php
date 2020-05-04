<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\PhoneDataTransformer;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\TestCase;

class PhoneDataTransformerTest extends TestCase
{
    /** @var PhoneDataTransformer */
    private $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new PhoneDataTransformer(PhoneNumberUtil::getInstance());
    }

    public function testTransform(): void
    {
        self::assertEquals(
            '+37060000000',
            $this->transformer->transform('+370 (600) 00 000'),
            'Test valid phone number.'
        );
        self::assertEquals(
            '123',
            $this->transformer->transform('123'),
            'Test invalid phone number.'
        );
        self::assertEquals(
            null,
            $this->transformer->transform(null),
            'Test null phone number.'
        );
    }

    public function testReverseTransform(): void
    {
        self::assertEquals(
            '+370 600 00000',
            $this->transformer->reverseTransform('+370 (600) 00 000'),
            'Test valid phone number.'
        );
        self::assertEquals(
            '123',
            $this->transformer->reverseTransform('123'),
            'Test invalid phone number.'
        );
        self::assertEquals(
            null,
            $this->transformer->reverseTransform(null),
            'Test null phone number.'
        );
    }
}
