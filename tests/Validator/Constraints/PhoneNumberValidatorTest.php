<?php

namespace App\Validator\Constraints;

use function array_merge;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): PhoneNumberValidator
    {
        return new PhoneNumberValidator(PhoneNumberUtil::getInstance());
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new PhoneNumber());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new PhoneNumber());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getValid
     */
    public function testValid(string $phone): void
    {
        $this->validator->validate($phone, new PhoneNumber());

        $this->assertNoViolation();
    }

    public function getValid(): array
    {
        return [
            ['+37060000000'],
            ['+370 700 55775'],
            ['+1-202-555-0147'],
        ];
    }

    /**
     * @dataProvider getInvalid
     */
    public function testInvalid(string $phone, string $code, array $options = []): void
    {
        $constraint = new PhoneNumber(
            array_merge(
                [
                    'invalidNumberMessage' => 'myMessage',
                ],
                $options
            )
        );

        $this->validator->validate($phone, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', $phone)
            ->setCode($code)
            ->assertRaised();
    }

    public function getInvalid(): array
    {
        return [
            ['1', PhoneNumber::NOT_PHONE_NUMBER_ERROR],
            ['+370600000', PhoneNumber::INVALID_PHONE_ERROR],
        ];
    }
}
