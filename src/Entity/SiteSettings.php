<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
#[ORM\Table(name: 'site_setting')]
class SiteSettings implements \Serializable
{

    private array $encoders;
    private array $normalizers;
    private Serializer $serializer;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'id')]
    #[ORM\JoinColumn(name: 'site_settings_user', referencedColumnName: 'id', nullable: false, onDelete:'CASCADE')]
    private ?Users $site_settings_user = null;

    #[ORM\Column(length: 20)]
    private ?string $settings_name = null;

    #[ORM\Column(length: 100)]
    private ?string $site_name = null;

    #[ORM\Column(name: 'site_setting', type: 'json')]
    private array $site_setting = [];

    private ?string $media_test_enabled = null;

    public function __construct()
    {
        $this->encoders    = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];

        $this->serializer  = new Serializer($this->normalizers, $this->encoders);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->site_settings_user;
    }

    public function setUser(Users $user): static
    {
        $this->site_settings_user = $user;

        return $this;
    }

    public function getSettingsName() : ?string
    {
        return $this->settings_name;
    }

    public function setSettingsName(string $name) :self{
        $this->settings_name = $name;
        return $this;
    }

    public function getSiteName() : ?string
    {
        return $this->site_name;
    }

    public function setSiteName(string $site_name) :self{
        $this->site_name = $site_name;
        return $this;
    }

    public function getSiteSetting(): array
    {
        return $this->site_setting;
    }

    public function setSiteSetting(array $site_setting): static
    {
        $this->site_setting = $site_setting;

        return $this;
    }

    public function getSiteGeneralSiteShowNavBar(): string
    {
        return $this->site_setting['general']['show_navbar'];
    }

    public function setSiteGeneralSiteShowNavBar(string $show): self
    {
        $this->site_setting['general']['show_navbar'] = $show;
        return $this;
    }

    public function getSiteGeneralSiteName(): string
    {
        return $this->site_setting['general']['site_name'];
    }

    public function setSiteGeneralSiteName(string $name): self
    {
        $this->site_setting['general']['site_name'] = $name;
        return $this;
    }

    public function getSiteGeneralSiteShowSlogan(): string
    {
        return $this->site_setting['general']['show_slogan_div'];
    }

    public function setSiteGeneralSiteShowSlogan(string $show): self
    {
        $this->site_setting['general']['show_slogan_div'] = $show;
        return $this;
    }

    public function getSiteGeneralSiteSlogan(): string
    {
        return $this->site_setting['general']['slogan'];
    }

    public function setSiteGeneralSiteSlogan(string $slogan): self
    {
        $this->site_setting['general']['slogan'] = $slogan;
        return $this;
    }

    public function getSiteGeneralSiteShowBrand(): string
    {
        return $this->site_setting['general']['show_brand_div'];
    }

    public function setSiteGeneralSiteShowBrand(string $show): self
    {
        $this->site_setting['general']['show_brand_div'] = $show;
        return $this;
    }

    public function getSiteGeneralSiteShowBrandText(): string
    {
        return $this->site_setting['general']['brand_text'];
    }

    public function setSiteGeneralSiteShowBrandText(string $show): self
    {
        $this->site_setting['general']['brand_text'] = $show;
        return $this;
    }

    public function getSiteGeneralSiteShowBrandImg(): string
    {
        return $this->site_setting['general']['brand_image'];
    }

    public function setSiteGeneralSiteShowBrandImg(string $show): self
    {
        $this->site_setting['general']['brand_image'] = $show;
        return $this;
    }

    public function getSiteGeneralSiteBrandImgPath(): string
    {
        return $this->site_setting['general']['brand_image_path'];
    }

    public function setSiteGeneralSiteBrandImgPath(string $path): self
    {
        $this->site_setting['general']['brand_image_path'] = $path;
        return $this;
    }

    public function serialize(): string
    {
        // TODO: Implement __serialize() method.
        /*return array($this->id, $this->site_name, $this->site_setting, $this->video_general_setting, $this->live_stream_setting);*/

        return serialize( $this->__serialize() );
    }

    public function unserialize($data): void
    {
        // TODO: Implement __unserialize() method.
        /*list ($this->id, $this->site_name, $this->site_setting, $this->video_general_setting, $this->live_stream_setting) = unserialize($data);*/
        $this->__unserialize( unserialize( $data ) );
    }

    public function __serialize(): array
    {
        // TODO: Implement __serialize() method.
        return get_object_vars( $this );
    }

    public function __unserialize($data): void
    {
        // TODO: Implement __unserialize() method.
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }
}
