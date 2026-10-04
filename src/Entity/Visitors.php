<?php

namespace App\Entity;

use App\Repository\VisitorsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitorsRepository::class)]
#[ORM\Table(name: 'visitors')]
class Visitors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:'ipv4', length: 20, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(name:'ipv6', length: 200, nullable: true)]
    private ?string $ipv6 = null;

    #[ORM\Column(name:'country', length: 150, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(name:'city', length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name:'code_postal', length: 20, nullable: true)]
    private ?string $code_postal = null;

    #[ORM\Column(name:'latitude', length: 100, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(name:'longitude', length: 100, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column(name:'user_agent', length: 255, nullable: true)]
    private ?string $user_agent = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $visit_date = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_mobile = null;

    public function serialize(): string
    {
        return serialize(array($this->id, $this->ip, $this->ipv6, $this->country, $this->city, $this->code_postal, $this->lat, $this->lng, $this->user_agent, $this->visit_date));
    }

    public function unserialize($data): void
    {
        list ($this->id, $this->ip, $this->ipv6, $this->country, $this->city, $this->code_postal, $this->lat, $this->lng, $this->user_agent, $this->visit_date) = unserialize($data);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIpv6(): ?string
    {
        return $this->ipv6;
    }

    public function setIpv6(?string $ipv6): static
    {
        $this->ipv6 = $ipv6;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code): static
    {
        $this->code_postal = $code;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): static
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getVisitDate(): ?\DateTimeImmutable
    {
        $this->visit_date->setTime(

            (int) $this->visit_date->format('H'), // Hour
            (int) $this->visit_date->format('i'), // Minute
            (int) $this->visit_date->format('s') // second
        );

        $this->visit_date->setDate(
            (int) $this->visit_date->format('Y'), // Year
            (int) $this->visit_date->format('m'), // Month
            (int) $this->visit_date->format('d') // day
        );

        return $this->visit_date;
    }

    public function setVisitDate(): static
    {
        $this->visit_date =  \DateTimeImmutable::createFromFormat('Y-m-d[H:is]', date('Y-m-d[H:is]'));

        return $this;
    }

    public function isMobile(): ?bool{
        return $this->is_mobile;
    }

    public function setIsMobile(?bool $mobile): static{
        $this->is_mobile = $mobile;
        return $this;
    }

}
