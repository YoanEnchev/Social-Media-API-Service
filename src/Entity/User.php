<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * 
 * @UniqueEntity(
 *      fields={"email"},
 *      message="This email is already taken!"
 * )
 * 
 * Implementing 
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $apiToken;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     * )
     */
    private $followers;

    public function __construct(array $params)
    {
        $this->setFirstName($params['first_name']);
        $this->setLastName($params['last_name']);
        $this->setEmail($params['email']);
        
        // Generate unique random token
        // mb5 - hash string
        // uniqid - generates unique string based on timestamp. Not cryptographically secure.
        // random_bytes - generates unique string which is cryptographically secure.
        //
        // So the generated string is always unique and secure.
        $this->setApiToken(md5(uniqid() . random_bytes(30)));
        $this->followers = new PersistentCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Methods for implementing the UserInterface so $passwordHasher->hashPassword can be used.
     * 
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /** 
     * Only for the sake of interface implementation. 
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return PersistentCollection|User[]
     */
    public function getFollowers(): PersistentCollection
    {
        return $this->followers;
    }

    public function addFollower(User $user): self
    {
        if (!$this->followers->contains($user)) {
            $this->followers[] = $user;
        }

        return $this;
    }
    public function removeFollower(User $user): self
    {
        $this->followers->removeElement($user);
        
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name'=> $this->lastName
        ];
    }
}
