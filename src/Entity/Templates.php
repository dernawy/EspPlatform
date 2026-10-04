<?php

namespace App\Entity;

use App\Repository\TemplatesRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints\Collection;

#[ORM\Entity(repositoryClass: TemplatesRepository::class)]
#[ORM\Table(name: 'templates')]
class Templates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $template_active = null;

    #[ORM\Column(name: 'template_name', length: 255)]
    private ?string $template_name = null;

    #[ORM\OneToMany(targetEntity: TemplatesPositions::class, mappedBy: 'template', cascade: ['persist', 'remove'])]
    #[ORM\Column(nullable: true)]
    private ?TemplatesPositions $t_position = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'template_user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $template_user = null;

    #[ORM\Column(name: 'site_name', length: 255)]
    private ?string $site_name = null;

    #[ORM\Column(name: 'site_slogan', length: 255)]
    private ?string $site_slogan = null;

    #[ORM\Column(name: 'positions', nullable: true)]
    private array $positions = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->template_user;
    }

    public function setUser(Users $user): static
    {
        $this->template_user = $user;

        return $this;
    }

    public function getTemplateName(): ?string
    {
        return $this->template_name;
    }

    public function setTemplateName(string $name): static
    {
        $this->template_name = $name;

        return $this;
    }

    public function getTemplatePosition(): ?TemplatesPositions
    {
        return $this->t_position;
    }

    public function setTemplatePosition(TemplatesPositions $position): static
    {
        $this->t_position = $position;

        return $this;
    }

    public function getSiteName(): ?string
    {
        return $this->site_name;
    }

    public function setSiteName(string $site_name): static
    {
        $this->site_name = $site_name;

        return $this;
    }

    public function getSiteSlogan(): ?string
    {
        return $this->site_slogan;
    }

    public function setSiteSlogan(string $site_slogan): static
    {
        $this->site_slogan = $site_slogan;

        return $this;
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function setPositions(array $positions): static
    {
        $this->positions = $positions;

        return $this;
    }

    public function addToPositions(string $positions): static
    {
        $this->positions[] = $positions;

        return $this;
    }

    public function isTemplateActive(): ?bool
    {
        return $this->template_active;
    }

    public function setTemplateActive(bool $template_active): static
    {
        $this->template_active = $template_active;

        return $this;
    }

    public function __toString(): string
    {
        return $this->template_name;
    }
}
