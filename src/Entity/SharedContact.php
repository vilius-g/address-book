<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SharedContactRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"contact_id", "shared_with_id"})})
 * @UniqueEntity({"contact", "sharedWith"})
 */
class SharedContact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="sharedContacts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sharedContacts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sharedWith;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getSharedWith(): ?User
    {
        return $this->sharedWith;
    }

    public function setSharedWith(?User $sharedWith): self
    {
        $this->sharedWith = $sharedWith;

        return $this;
    }
}
