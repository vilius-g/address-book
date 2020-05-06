<?php

namespace App\Tests\Validator\Constraints;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validator\Constraints\ExistingUserEmail;
use App\Validator\Constraints\ExistingUserEmailValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ExistingUserEmailValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ExistingUserEmailValidator
    {
        /** @var UserRepository|MockObject $userRepository */
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findOneByEmail')->willReturnMap(
            [
                ['existing-user@example.com', $this->createUserWithEmail('existing-user@example.com')],
                ['missing-user@example.com', null],
            ]
        );

        return new ExistingUserEmailValidator($userRepository);
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new ExistingUserEmail());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new ExistingUserEmail());

        $this->assertNoViolation();
    }

    public function testValid(): void
    {
        $this->validator->validate('existing-user@example.com', new ExistingUserEmail());

        $this->assertNoViolation();
    }

    public function testInvalid(): void
    {
        $value = 'missing-user@example.com';
        $this->validator->validate($value, new ExistingUserEmail());

        $this->buildViolation('User with {{ value }} not found.')
            ->setParameter('{{ value }}', $value)
            ->assertRaised();
    }

    /**
     * Create user instance with provided email.
     */
    protected function createUserWithEmail(string $email): User
    {
        $user = new User();
        $user->setEmail($email);

        return $user;
    }
}
