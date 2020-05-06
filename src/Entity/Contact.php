<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints\PhoneNumber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Represents stored contact information.
 *
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     collectionOperations={
 *         "get",
 *         "post"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or object.getOwner() == user"},
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or object.getOwner() == user or object.isSharedWith(user)"},
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object.getOwner() == user or object.isSharedWith(user)"},
 *         "put"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or (object.getOwner() == user and previous_object.getOwner() == user)"},
 *         "patch"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or (object.getOwner() == user and previous_object.getOwner() == user)"},
 *     },
 *     normalizationContext={"groups"={"contact:output"}},
 *     denormalizationContext={"groups"={"contact:input"}}
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"contact:output"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"contact:input", "contact:output", "shared:output"})
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contact:input", "contact:output", "shared:output"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     * @PhoneNumber()
     * @Groups({"contact:input", "contact:output", "shared:output"})
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(
     *     name="shared_contact",
     *     joinColumns={@ORM\JoinColumn(name="contact_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="shared_with_id")}
     *     )
     * @Groups({"contact:output"})
     */
    private $sharedContacts;

    public function __construct()
    {
        $this->sharedContacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|SharedContact[]
     */
    public function getSharedContacts(): Collection
    {
        return $this->sharedContacts;
    }

    /**
     * Has this contact been shared with given user.
     *
     * @param User $user
     * @return bool
     */
    public function isSharedWith(User $user): bool
    {
        return $this->sharedContacts->contains($user);
    }
}
