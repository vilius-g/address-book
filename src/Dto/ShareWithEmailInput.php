<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Contact;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ShareWithEmailInput
{
    /**
     * @var Contact
     * @ApiProperty()
     * @Groups({"shared:input", "admin:input"})
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    public $contact;

    /**
     * @var UserEmailInput
     * @ApiProperty()
     * @Groups({"shared:input", "admin:input"})
     * @Assert\NotNull()
     */
    public $sharedWith;
}
