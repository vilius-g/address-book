<?php

declare(strict_types=1);

namespace App\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
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
     * {@inheritdoc}
     */
    public function transform($value)
    {
        try {
            return $this->phoneNumberUtil->format($this->parse($value), PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        try {
            return $this->phoneNumberUtil->format($this->parse($value), PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException $e) {
            return $value;
        }
    }

    /**
     * Parse phone number string to PhoneNumber instance.
     *
     * @param mixed $value Raw phone string
     *
     * @throws NumberParseException
     */
    private function parse($value): PhoneNumber
    {
        return $this->phoneNumberUtil->parse($value);
    }
}
