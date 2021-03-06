<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents system user.
 *
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="true","validation_groups"={"Default", "create"}}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or object == user", "requirements"={"id"="\d+"}},
 *         "get_me"={
 *             "method"="GET",
 *             "path"="/users/me",
 *             "controller"=\App\Controller\Api\User\InfoController::class,
 *             "openapi_context"={
 *                 "parameters"={}
 *             },
 *             "read"=false,
 *             "security"="is_granted('ROLE_USER')"
 *         },
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object == user"},
 *         "patch"={"security"="is_granted('ROLE_ADMIN') or object == user"},
 *     },
 *     normalizationContext={"groups"={"user:output"}},
 *     denormalizationContext={"groups"={"user:input"}}
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:output"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"user:input","user:output","shared:output"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:output"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"update"})
     */
    private $password;

    /**
     * @var string Plain password (for setting one)
     * @Groups({"user:input"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create"})
     */
    private $passwordPlain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->passwordPlain = null;
    }

    /**
     * @return string
     */
    public function getPasswordPlain(): ?string
    {
        return $this->passwordPlain;
    }

    public function setPasswordPlain(string $passwordPlain): void
    {
        $this->passwordPlain = $passwordPlain;
    }
}
