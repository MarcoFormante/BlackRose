<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260916191329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, whatsapp VARCHAR(255) NOT NULL, instagram VARCHAR(255) NOT NULL, facebook VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE artist_image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, artist_id INT NOT NULL, INDEX IDX_A531340CB7970CF8 (artist_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mix_image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE studio_image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE artist_image ADD CONSTRAINT FK_A531340CB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_image DROP FOREIGN KEY FK_A531340CB7970CF8');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE artist_image');
        $this->addSql('DROP TABLE mix_image');
        $this->addSql('DROP TABLE studio_image');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
