<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261013201445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devices (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, model VARCHAR(100) NOT NULL, revision VARCHAR(50) NOT NULL, cpu VARCHAR(10) NOT NULL, flash_total_memory INT NOT NULL, flash_used_memory INT NOT NULL, flash_free_memory INT NOT NULL, free_heap INT NOT NULL, min_heap INT NOT NULL, has_psram TINYINT NOT NULL, psram_size INT NOT NULL, used_psram INT NOT NULL, free_psram INT NOT NULL, sdk_version VARCHAR(100) NOT NULL, ap_ssid VARCHAR(50) NOT NULL, ap_ip VARCHAR(20) NOT NULL, ap_netmask VARCHAR(20) NOT NULL, ap_gateway VARCHAR(20) NOT NULL, devices_user INT NOT NULL, INDEX IDX_11074E9A665D8378 (devices_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `navbar` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default TINYINT NOT NULL, show_language TINYINT NOT NULL, show_circle_img TINYINT NOT NULL, show_logo TINYINT NOT NULL, show_search TINYINT NOT NULL, items JSON NOT NULL, items_count INT NOT NULL, items_color VARCHAR(20) NOT NULL, bg_color VARCHAR(20) NOT NULL, logo_path VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_setting (id INT AUTO_INCREMENT NOT NULL, settings_name VARCHAR(20) NOT NULL, site_name VARCHAR(100) NOT NULL, site_setting JSON NOT NULL, temp_id INT NOT NULL, site_settings_user INT NOT NULL, INDEX IDX_64D05A534EEFBAED (temp_id), INDEX IDX_64D05A53BBD3F8D5 (site_settings_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, template_active TINYINT NOT NULL, template_name VARCHAR(255) NOT NULL, template_user INT NOT NULL, INDEX IDX_6F287D8E585DFC24 (template_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_settings (id INT AUTO_INCREMENT NOT NULL, user_ip VARCHAR(20) NOT NULL, user_ipv6 VARCHAR(200) NOT NULL, profile_settings JSON DEFAULT NULL, network_settings JSON DEFAULT NULL, user INT NOT NULL, style_id INT NOT NULL, INDEX IDX_5C844C58D93D649 (user), INDEX IDX_5C844C5BACD6074 (style_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_styles (id INT AUTO_INCREMENT NOT NULL, template_name VARCHAR(50) NOT NULL, style_name VARCHAR(50) NOT NULL, style_data JSON NOT NULL, style_user INT NOT NULL, INDEX IDX_D308E1C8E21FA7B8 (style_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, full_name VARCHAR(100) DEFAULT NULL, username VARCHAR(30) NOT NULL, user_email VARCHAR(180) NOT NULL, phone VARCHAR(20) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, salt VARCHAR(32) NOT NULL, enabled TINYINT NOT NULL, activated TINYINT NOT NULL, banned TINYINT NOT NULL, terms_accepted TINYINT NOT NULL, connected TINYINT NOT NULL, register_date DATETIME DEFAULT NULL, last_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9D17F50A6 (uuid), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, ipv4 VARCHAR(20) DEFAULT NULL, ipv6 VARCHAR(200) DEFAULT NULL, country VARCHAR(150) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, code_postal VARCHAR(20) DEFAULT NULL, latitude VARCHAR(100) DEFAULT NULL, longitude VARCHAR(100) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, visit_date DATETIME DEFAULT NULL, is_mobile TINYINT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE devices ADD CONSTRAINT FK_11074E9A665D8378 FOREIGN KEY (devices_user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site_setting ADD CONSTRAINT FK_64D05A534EEFBAED FOREIGN KEY (temp_id) REFERENCES templates (id)');
        $this->addSql('ALTER TABLE site_setting ADD CONSTRAINT FK_64D05A53BBD3F8D5 FOREIGN KEY (site_settings_user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8E585DFC24 FOREIGN KEY (template_user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C58D93D649 FOREIGN KEY (user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5BACD6074 FOREIGN KEY (style_id) REFERENCES user_styles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_styles ADD CONSTRAINT FK_D308E1C8E21FA7B8 FOREIGN KEY (style_user) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devices DROP FOREIGN KEY FK_11074E9A665D8378');
        $this->addSql('ALTER TABLE site_setting DROP FOREIGN KEY FK_64D05A534EEFBAED');
        $this->addSql('ALTER TABLE site_setting DROP FOREIGN KEY FK_64D05A53BBD3F8D5');
        $this->addSql('ALTER TABLE templates DROP FOREIGN KEY FK_6F287D8E585DFC24');
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C58D93D649');
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C5BACD6074');
        $this->addSql('ALTER TABLE user_styles DROP FOREIGN KEY FK_D308E1C8E21FA7B8');
        $this->addSql('DROP TABLE devices');
        $this->addSql('DROP TABLE `navbar`');
        $this->addSql('DROP TABLE site_setting');
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE user_styles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visitors');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
