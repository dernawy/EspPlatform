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

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'template_user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $template_user = null;
    #[ORM\Column(name: 'template_active', type:'boolean')]
    private ?bool $template_active = null;

    #[ORM\Column(name: 'template_name', length: 255)]
    private ?string $template_name = null;



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
