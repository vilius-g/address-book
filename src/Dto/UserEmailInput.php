<?php
declare(strict_types=1);

namespace App\Dto;

use App\Validator\Constraints\ExistingUserEmail;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserEmailInput
{
    /**
     * @var string
     * @Groups({"shared:input", "admin:input"})
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @ExistingUserEmail()
     */
    public $email;
}
