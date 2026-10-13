<?php

namespace App\Entity;

use App\Repository\UserStylesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStylesRepository::class)]
class UserStyles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'style_user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $style_user = null;

    #[ORM\Column(length: 50)]
    private ?string $template_name = null;

    #[ORM\Column(length: 50)]
    private ?string $style_name = null;

    #[ORM\Column]
    private array $style_data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->style_user;
    }

    public function setUser(Users $user): static
    {
        $this->style_user = $user;

        return $this;
    }

    public function getTemplateName(): ?string
    {
        return $this->template_name;
    }

    public function setTemplateName(string $template_name): static
    {
        $this->template_name = $template_name;

        return $this;
    }

    public function getStyleName(): ?string
    {
        return $this->style_name;
    }

    public function setStyleName(string $style_name): static
    {
        $this->style_name = $style_name;

        return $this;
    }

    public function getStyleData(): array
    {
        return $this->style_data;
    }

    public function setStyleData(array $style_data): static
    {
        $this->style_data = $style_data;

        return $this;
    }
}
