<?php

namespace App\Entity;

use App\Repository\DevicesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevicesRepository::class)]
class Devices
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'devices_user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $devices_user = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    private ?string $revision = null;

    #[ORM\Column(length: 10)]
    private ?string $cpu = null;

    #[ORM\Column]
    private ?int $flash_total_memory = null;

    #[ORM\Column]
    private ?int $flash_used_memory = null;

    #[ORM\Column]
    private ?int $flash_free_memory = null;

    #[ORM\Column]
    private ?int $free_heap = null;

    #[ORM\Column]
    private ?int $min_heap = null;

    #[ORM\Column]
    private ?bool $has_psram = null;

    #[ORM\Column]
    private ?int $psram_size = null;

    #[ORM\Column]
    private ?int $used_psram = null;

    #[ORM\Column]
    private ?int $free_psram = null;

    #[ORM\Column(length: 100)]
    private ?string $sdk_version = null;

    #[ORM\Column(length: 50)]
    private ?string $ap_ssid = null;

    #[ORM\Column(length: 20)]
    private ?string $ap_ip = null;

    #[ORM\Column(length: 20)]
    private ?string $ap_netmask = null;

    #[ORM\Column(length: 20)]
    private ?string $ap_gateway = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->devices_user;
    }

    public function setUser(Users $user): static
    {
        $this->devices_user = $user;

        return $this;
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

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getRevision(): ?string
    {
        return $this->revision;
    }

    public function setRevision(string $revision): static
    {
        $this->revision = $revision;

        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(string $cpu): static
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getFlashTotalMemory(): ?int
    {
        return $this->flash_total_memory;
    }

    public function setFlasTotalMemory(int $flas_total_memory): static
    {
        $this->flash_total_memory = $flas_total_memory;

        return $this;
    }

    public function getFlashUsedMemory(): ?int
    {
        return $this->flash_used_memory;
    }

    public function setFlashUsedMemory(int $flash_used_memory): static
    {
        $this->flash_used_memory = $flash_used_memory;

        return $this;
    }

    public function getFlashFreeMemory(): ?int
    {
        return $this->flash_free_memory;
    }

    public function setFlashFreeMemory(int $flash_free_memory): static
    {
        $this->flash_free_memory = $flash_free_memory;

        return $this;
    }

    public function getFreeHeap(): ?int
    {
        return $this->free_heap;
    }

    public function setFreeHeap(int $free_heap): static
    {
        $this->free_heap = $free_heap;

        return $this;
    }

    public function getMinHeap(): ?int
    {
        return $this->min_heap;
    }

    public function setMinHeap(int $min_heap): static
    {
        $this->min_heap = $min_heap;

        return $this;
    }

    public function hasPsram(): ?bool
    {
        return $this->has_psram;
    }

    public function setHasPsram(bool $has_psram): static
    {
        $this->has_psram = $has_psram;

        return $this;
    }

    public function getPsramSize(): ?int
    {
        return $this->psram_size;
    }

    public function setPsramSize(int $psram_size): static
    {
        $this->psram_size = $psram_size;

        return $this;
    }

    public function getUsedPsram(): ?int
    {
        return $this->used_psram;
    }

    public function setUsedPsram(int $used_psram): static
    {
        $this->used_psram = $used_psram;

        return $this;
    }

    public function getFreePsram(): ?int
    {
        return $this->free_psram;
    }

    public function setFreePsram(int $free_psram): static
    {
        $this->free_psram = $free_psram;

        return $this;
    }

    public function getSdkVersion(): ?string
    {
        return $this->sdk_version;
    }

    public function setSdkVersion(string $sdk_version): static
    {
        $this->sdk_version = $sdk_version;

        return $this;
    }

    public function getApSsid(): ?string
    {
        return $this->ap_ssid;
    }

    public function setApSsid(string $ap_ssid): static
    {
        $this->ap_ssid = $ap_ssid;

        return $this;
    }

    public function getApIp(): ?string
    {
        return $this->ap_ip;
    }

    public function setApIp(string $ap_ip): static
    {
        $this->ap_ip = $ap_ip;

        return $this;
    }

    public function getApNetmask(): ?string
    {
        return $this->ap_netmask;
    }

    public function setApNetmask(string $ap_netmask): static
    {
        $this->ap_netmask = $ap_netmask;

        return $this;
    }

    public function getApGateway(): ?string
    {
        return $this->ap_gateway;
    }

    public function setApGateway(string $ap_gateway): static
    {
        $this->ap_gateway = $ap_gateway;

        return $this;
    }
}
