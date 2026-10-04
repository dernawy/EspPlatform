<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['Uuid'], message: 'There is already an account with this uuid')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'uuid', type: 'uuid', unique: true)]
    private ?Uuid $Uuid = null;

    #[Assert\Regex(pattern: '/^[A-Za-z]+([\ A-Za-z]+){0,2}$/i', message: 'The name cannot contain a number.')]
    #[ORM\Column(name: 'full_name', type: 'string', length: 100, nullable: true)]
    private ?string $full_name = null;

    #[ORM\Column(name: 'username', type: 'string', length: 30, unique: true)]
    private ?string $username = null;

    #[Assert\Regex('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', message: 'The email format is not correct, the correct format is like [example@example_domain.(com/net/fr ... etc).')]
    #[ORM\Column(name: 'user_email', type: 'string', length: 180)]
    private ?string $user_email = null;

    #[Assert\Regex('/^\+\d{2}?\d{9}$/', message: 'The phone number must be in this form [+33636128877].')]
    #[ORM\Column(name: 'phone', type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[Assert\NotBlank(message: 'New password can not be blank, please chose a strong password between [6-64] characters.')]
    #[Assert\Regex('#^\S*(?=\S{6,64})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', message: 'The password you entered ({{ value }}), is not valid, the password must be between [6-64] characters, contain minimum [1] number, [1] capital and [1] special character.')]
    #[ORM\Column(name: 'password', type: 'string', length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(name: 'roles', type: 'json')]
    private array $roles = [];

    #[ORM\Column(name:'salt', type:'string', length: 32)]
    private ?string $salt = null;

    #[ORM\Column(name:'enabled', type:'boolean')]
    private ?bool $enabled = null;

    #[ORM\Column(name:'activated', type:'boolean')]
    private ?bool $activated = null;

    #[ORM\Column(name:'banned', type:'boolean')]
    private ?bool $banned = null;

    #[ORM\Column]
    private ?bool $terms_accepted = null;

    #[ORM\Column(name:'connected', type:'boolean')]
    private ?bool $connected = null;

    #[ORM\Column(name: 'register_date', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $register_date = null;

    #[ORM\Column(name: 'last_update', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $last_update = null;

    public function __construct()
    {
        $this->enabled             = true;
        $this->activated           = false;
        $this->connected           = false;
        $this->banned              = false;
        $this->salt                = md5(uniqid(null, true));
    }

    public function __unserialize(array $data): void
    {
        $this->id            = $data['id'] ?? null;
        $this->username      = $data['username'] ?? null;
        $this->user_email    = $data['user_email'] ?? null;
        $this->password      = $data['password'] ?? null;
        $this->roles         = $data['roles'] ?? [];
    }

    public function __serialize(): array
    {

        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'user_email'    => $this->user_email,    // or $this->username depending on your setup
            'password'      => $this->password, // keeps session valid if password changes
            'roles'         => $this->roles,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->Uuid;
    }

    public function setUuid(Uuid $Uuid): static
    {
        $this->Uuid = $Uuid;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $FullName): static
    {
        $this->full_name = $FullName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $Username): static
    {
        $this->username = $Username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    public function setUserEmail(string $UserEmail): static
    {
        $this->user_email = $UserEmail;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $Password): static
    {
        $this->password = $Password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $Roles): static
    {
        $this->roles = $Roles;

        return $this;
    }

    public function getRole(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $Salt): static
    {
        $this->salt = $Salt;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $Enabled): static
    {
        $this->enabled = $Enabled;

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $Activated): static
    {
        $this->activated = $Activated;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(bool $Banned): static
    {
        $this->banned = $Banned;

        return $this;
    }

    public function isTermsAccepted(): ?bool
    {
        return $this->terms_accepted;
    }

    public function setTermsAccepted(bool $TermsAccepted): static
    {
        $this->terms_accepted = $TermsAccepted;

        return $this;
    }

    public function getConnected(): ?bool
    {
        return $this->connected;
    }

    public function setConnected(bool $connected): self
    {
        $this->connected = $connected;

        return $this;
    }

    public function getRegisterDate(): ?\DateTimeImmutable
    {
        $this->register_date->setTime(

            (int) $this->register_date->format('H'), // Hour
            (int) $this->register_date->format('i'), // Minute
            (int) $this->register_date->format('s') // second
        );

        $this->register_date->setDate(
            (int) $this->register_date->format('Y'), // Year
            (int) $this->register_date->format('m'), // Month
            (int) $this->register_date->format('d') // day
        );

        return $this->register_date;
    }

    public function setRegisterDate(): static
    {
        $this->register_date =  \DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d'));

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeImmutable
    {
        return $this->last_update;
    }

    public function setLastUpdate(\DateTimeImmutable $last_update): static
    {
        $this->last_update = $last_update;

        return $this;
    }

}
