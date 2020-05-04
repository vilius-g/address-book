<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PhoneNumber extends Constraint
{
    public const NOT_PHONE_NUMBER_ERROR = '73b8c5e7-0aa4-4bc2-aeb6-e04374b9284c';
    public const INVALID_PHONE_ERROR = '45cd010d-e7fb-43e8-b16f-055c817472e0';

    public $invalidNumberMessage = 'Not valid phone number.';

    protected static $errorNames = [
        self::NOT_PHONE_NUMBER_ERROR => 'NOT_PHONE_NUMBER_ERROR',
        self::INVALID_PHONE_ERROR => 'INVALID_PHONE_ERROR',
    ];
}
