<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002235937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_setting (id INT AUTO_INCREMENT NOT NULL, settings_name VARCHAR(20) NOT NULL, site_name VARCHAR(100) NOT NULL, site_setting JSON NOT NULL, site_settings_user INT NOT NULL, INDEX IDX_64D05A53BBD3F8D5 (site_settings_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, template_active TINYINT NOT NULL, template_name VARCHAR(255) NOT NULL, t_position VARCHAR(255) DEFAULT NULL, site_name VARCHAR(255) NOT NULL, site_slogan VARCHAR(255) NOT NULL, positions JSON DEFAULT NULL, template_user INT NOT NULL, INDEX IDX_6F287D8E585DFC24 (template_user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE templates_positions (id INT AUTO_INCREMENT NOT NULL, position_name VARCHAR(50) NOT NULL, position_alias VARCHAR(50) NOT NULL, position_active TINYINT NOT NULL, temp_id INT NOT NULL, INDEX IDX_354E1CBE4EEFBAED (temp_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_settings (id INT AUTO_INCREMENT NOT NULL, user_ip VARCHAR(20) NOT NULL, user_ipv6 VARCHAR(200) NOT NULL, style_name VARCHAR(50) NOT NULL, site_style JSON DEFAULT NULL, profile_settings JSON DEFAULT NULL, network_settings JSON DEFAULT NULL, user INT NOT NULL, INDEX IDX_5C844C58D93D649 (user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, ipv4 VARCHAR(20) DEFAULT NULL, ipv6 VARCHAR(200) DEFAULT NULL, country VARCHAR(150) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, code_postal VARCHAR(20) DEFAULT NULL, latitude VARCHAR(100) DEFAULT NULL, longitude VARCHAR(100) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, visit_date DATETIME DEFAULT NULL, is_mobile TINYINT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE site_setting ADD CONSTRAINT FK_64D05A53BBD3F8D5 FOREIGN KEY (site_settings_user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8E585DFC24 FOREIGN KEY (template_user) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE templates_positions ADD CONSTRAINT FK_354E1CBE4EEFBAED FOREIGN KEY (temp_id) REFERENCES templates (id)');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C58D93D649 FOREIGN KEY (user) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_setting DROP FOREIGN KEY FK_64D05A53BBD3F8D5');
        $this->addSql('ALTER TABLE templates DROP FOREIGN KEY FK_6F287D8E585DFC24');
        $this->addSql('ALTER TABLE templates_positions DROP FOREIGN KEY FK_354E1CBE4EEFBAED');
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C58D93D649');
        $this->addSql('DROP TABLE site_setting');
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP TABLE templates_positions');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE visitors');
    }
}
