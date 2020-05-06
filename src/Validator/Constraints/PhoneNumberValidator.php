<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneNumberValidator extends ConstraintValidator
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PhoneNumber) {
            throw new UnexpectedTypeException($constraint, PhoneNumber::class);
        }

        if (null === $value || '' === $value) {
            // Do not validate empty or missing values, should be handled in other parts.
            return;
        }

        try {
            $phone = $this->phoneNumberUtil->parse($value);
        } catch (NumberParseException $e) {
            $this->context->buildViolation($constraint->invalidNumberMessage)
                ->setParameter('{{ value }}', $value)
                ->setCode($constraint::NOT_PHONE_NUMBER_ERROR)
                ->addViolation();

            return;
        }

        if (!$this->phoneNumberUtil->isValidNumber($phone)) {
            $this->context->buildViolation($constraint->invalidNumberMessage)
                ->setParameter('{{ value }}', $value)
                ->setCode($constraint::INVALID_PHONE_ERROR)
                ->addViolation();

            return;
        }
    }
}
