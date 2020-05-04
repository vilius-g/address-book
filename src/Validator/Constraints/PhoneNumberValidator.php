<?php

namespace App\Validator\Constraints;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneNumberValidator extends ConstraintValidator
{
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

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phone = $phoneUtil->parse($value);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (NumberParseException $e) {
            $this->context->buildViolation($constraint->invalidNumberMessage)
                ->setParameter('{{ value }}', $value)
                ->setCode($constraint::NOT_PHONE_NUMBER_ERROR)
                ->addViolation();

            return;
        }

        if (!$phoneUtil->isValidNumber($phone)) {
            $this->context->buildViolation($constraint->invalidNumberMessage)
                ->setParameter('{{ value }}', $value)
                ->setCode($constraint::INVALID_PHONE_ERROR)
                ->addViolation();

            return;
        }
    }
}
