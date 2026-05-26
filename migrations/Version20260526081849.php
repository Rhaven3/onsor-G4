<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260526081849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(5) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_NAME (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, limit_registration_date DATETIME DEFAULT NULL, description LONGTEXT NOT NULL, state VARCHAR(255) DEFAULT NULL, max_registration INT NOT NULL, organisator_id INT NOT NULL, address_id INT NOT NULL, site_id INT NOT NULL, INDEX IDX_7656F53BFFDD4EC8 (organisator_id), INDEX IDX_7656F53BF5B7AF75 (address_id), INDEX IDX_7656F53BF6BD1646 (site_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, mobile VARCHAR(255) NOT NULL, activate TINYINT NOT NULL, site_id INT NOT NULL, INDEX IDX_8D93D649F6BD1646 (site_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_trip (user_id INT NOT NULL, trip_id INT NOT NULL, INDEX IDX_CD7B9F2A76ED395 (user_id), INDEX IDX_CD7B9F2A5BC2E0E (trip_id), PRIMARY KEY (user_id, trip_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BFFDD4EC8 FOREIGN KEY (organisator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE user_trip ADD CONSTRAINT FK_CD7B9F2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_trip ADD CONSTRAINT FK_CD7B9F2A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BFFDD4EC8');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BF5B7AF75');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BF6BD1646');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F6BD1646');
        $this->addSql('ALTER TABLE user_trip DROP FOREIGN KEY FK_CD7B9F2A76ED395');
        $this->addSql('ALTER TABLE user_trip DROP FOREIGN KEY FK_CD7B9F2A5BC2E0E');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_trip');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
