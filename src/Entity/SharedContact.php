<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\ShareWithEmailInput;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents contact sharing relation.
 *
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     collectionOperations={
 *         "get",
 *         "post"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or object.getOwner() == user"},
 *         "share_with_email"={
 *             "method"="post",
 *             "path"="/shared_contacts/share_with_email",
 *             "input"=ShareWithEmailInput::class,
 *             "security_post_denormalize"="is_granted('ROLE_ADMIN') or object.getOwner() == user"
 *         }
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or object.getSharedWith() == user or object.getOwner() == user"},
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object.getSharedWith() == user or object.getOwner() == user"},
 *     },
 *     normalizationContext={"groups"={"shared:output"}},
 *     denormalizationContext={"groups"={"shared:input"}}
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\SharedContactRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"contact_id", "shared_with_id"})})
 * @UniqueEntity({"contact", "sharedWith"}, message="Already shared with this user.")
 */
class SharedContact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shared:output", "shared:input"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="sharedContacts", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"shared:output", "shared:input"})
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"shared:output", "shared:input"})
     * @Assert\NotEqualTo(propertyPath="owner", message="Cannot share with yourself.")
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

    public function getOwner(): ?User
    {
        return $this->getContact() ? $this->getContact()->getOwner() : null;
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
