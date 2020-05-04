<?php

namespace App\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Normalizes user-entered phone numbers to standard format for storage.
 */
class PhoneDataTransformer implements DataTransformerInterface
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
     * @inheritDoc
     */
    public function transform($value)
    {
        try {
            $phone = $this->phoneNumberUtil->parse($value);

            return $this->phoneNumberUtil->format($phone, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        try {
            $phone = $this->phoneNumberUtil->parse($value);

            return $this->phoneNumberUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException $e) {
            return $value;
        }
    }
}
