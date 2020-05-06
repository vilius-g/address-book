<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingUserEmailValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ExistingUserEmailValidator constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistingUserEmail) {
            throw new UnexpectedTypeException($constraint, ExistingUserEmail::class);
        }

        if (null === $value || '' === $value) {
            // Do not validate empty or missing values, should be handled in other parts.
            return;
        }

        if (!$this->userWithEmailExists($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }
    }

    /**
     * Check for user existence in the database.
     *
     * @param $value
     * @return bool
     */
    private function userWithEmailExists($value): bool
    {
        return null !== $this->userRepository->findOneByEmail($value);
    }
}
