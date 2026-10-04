<?php

namespace App\Entity;

use App\Repository\NavbarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NavbarRepository::class)]
#[ORM\Table(name: '`navbar`')]
class Navbar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 100)]
    private ?string $name = null;

    #[ORM\Column(name: 'is_default', type: 'boolean')]
    private ?bool $is_default = null;

    #[ORM\Column(name: 'show_language', type: 'boolean')]
    private ?bool $show_language = null;

    #[ORM\Column(name: 'show_circle_img', type: 'boolean')]
    private ?bool $show_circle_img = null;

    #[ORM\Column(name: 'show_logo', type: 'boolean')]
    private ?bool $show_logo = null;

    #[ORM\Column(name: 'show_search', type: 'boolean')]
    private ?bool $show_search = null;

    #[ORM\Column(name: 'items', type: 'json')]
    private ?array $items = null;

    #[ORM\Column(name: 'items_count', type: 'integer')]
    private ?int $items_count = null;

    #[ORM\Column(name: 'items_color', type: 'string', length: 20)]
    private ?string $items_color = null;

    #[ORM\Column(name: 'bg_color', type: 'string', length: 20)]
    private ?string $bg_color = null;

    #[ORM\Column(name: 'logo_path', type: 'string', length: 255)]
    private ?string $logo_path = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->is_default;
    }

    public function isShowLanguage(): ?bool
    {
        return $this->show_language;
    }

    public function setShowLanguage(bool $show_language): static
    {
        $this->show_language = $show_language;

        return $this;
    }

    public function isShowCircleImg(): ?bool
    {
        return $this->show_circle_img;
    }

    public function setShowCircleImg(bool $show_circle_img): static
    {
        $this->show_circle_img = $show_circle_img;

        return $this;
    }

    public function isShowLogo(): ?bool
    {
        return $this->show_logo;
    }

    public function setShowLogo(bool $show_logo): static
    {
        $this->show_logo = $show_logo;

        return $this;
    }

    public function isShowSearch(): ?bool
    {
        return $this->show_search;
    }

    public function setShowSearch(bool $show_search): static
    {
        $this->show_search = $show_search;

        return $this;
    }

    public function setDefault(bool $is_default): static
    {
        $this->is_default = $is_default;

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getItemsCount(): ?int
    {
        return $this->items_count;
    }

    public function setItemsCount(int $items_count): static
    {
        $this->items_count = $items_count;

        return $this;
    }

    public function getItemsColor(): ?string
    {
        return $this->items_color;
    }

    public function setItemsColor(string $items_color): static
    {
        $this->items_color = $items_color;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bg_color;
    }

    public function setBgColor(string $bg_color): static
    {
        $this->bg_color = $bg_color;

        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logo_path;
    }

    public function setLogoPath(string $logo_path): static
    {
        $this->logo_path = $logo_path;

        return $this;
    }
}

