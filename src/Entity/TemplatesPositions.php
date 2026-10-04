<?php

namespace App\Entity;

use App\Repository\TemplatesPositionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: TemplatesPositionsRepository::class)]
#[ORM\Table(name: 'templates_positions')]
class TemplatesPositions
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Templates::class, inversedBy: 't_position')]
    #[ORM\JoinColumn(name: 'temp_id', referencedColumnName: 'id', nullable: false)]
    private ?Templates $template = null;

    #[ORM\Column(name: 'position_name', length: 50)]
    private ?string $position_name = null;

    #[ORM\Column(name: 'position_alias', length: 50)]
    private ?string $position_alias = null;

    #[ORM\Column(name: 'position_active', ) ]
    private ?bool $position_active = null;

    private EntityManagerInterface $em;

    #[Pure] public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?Templates
    {
        return $this->template;
    }

    public function setTemplate(Templates $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getPositionName(): ?string
    {
        return $this->position_name;
    }

    public function setPositionName(string $position_name): static
    {
        $this->position_name = $position_name;


        return $this;
    }

    public function getPositionAlias(): ?string
    {
        return $this->position_alias;
    }

    public function setPositionAlias(string $position_alias): static
    {
        $this->position_alias = $position_alias;

        return $this;
    }

    public function isPositionActive(): ?bool
    {
        return $this->position_active;
    }

    public function setPositionActive(bool $position_active): static
    {
        $this->position_active = $position_active;

        return $this;
    }

    public function __toString(): string
    {
        return $this->position_name;
    }
}
