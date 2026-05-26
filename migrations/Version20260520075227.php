<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260520075227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY `FK_7656F53BA76ED395`');
        $this->addSql('DROP INDEX IDX_7656F53BA76ED395 ON trip');
        $this->addSql('ALTER TABLE trip DROP user_id');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD mobile VARCHAR(255) NOT NULL, ADD activate TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT `FK_7656F53BA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7656F53BA76ED395 ON trip (user_id)');
        $this->addSql('ALTER TABLE user DROP username, DROP first_name, DROP last_name, DROP mobile, DROP activate');
    }
}
