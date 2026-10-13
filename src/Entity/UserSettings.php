<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingsRepository::class)]
#[ORM\Table(name: 'user_settings')]
class UserSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $user = null;

    #[ORM\Column(name: 'user_ip', length: 20)]
    private ?string $user_ip = null;

    #[ORM\Column(name: 'user_ipv6', length: 200)]
    private ?string $user_ipv6 = null;

    #[ORM\Column(name: 'profile_settings', nullable: true)]
    private array $profile_settings = [];

    #[ORM\Column(name: 'network_settings', nullable: true)]
    private array $network_settings = [];

    #[ORM\ManyToOne(targetEntity: UserStyles::class, cascade: ['persist'], inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'style_id', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?UserStyles $user_style = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIp(): ?string
    {
        return $this->user_ip;
    }

    public function setUserIp(string $ip): static
    {
        $this->user_ip = $ip;

        return $this;
    }

    public function getIpv6(): ?string
    {
        return $this->user_ipv6;
    }

    public function setIpv6(?string $ipv6): static
    {
        $this->user_ipv6 = $ipv6;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUserStyle(): ?UserStyles
    {
        return $this->user_style;
    }

    public function setUserStyle(UserStyles $user_style): static
    {
        $this->user_style = $user_style;

        return $this;
    }

    public function getProfileSettings(): array
    {
        return $this->profile_settings;
    }

    public function setProfileSettings(array $profile_settings): static
    {
        $this->profile_settings = $profile_settings;

        return $this;
    }

    public function getNetworkSettings(): array
    {
        return $this->network_settings;
    }

    public function setNetworkSettings(array $network_settings): static
    {
        $this->network_settings = $network_settings;

        return $this;
    }
}
