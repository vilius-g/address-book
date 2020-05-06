<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that user with this email exists.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ExistingUserEmail extends Constraint
{
    public $message = 'User with {{ value }} not found.';
}
