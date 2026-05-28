<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528100556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY `FK_7656F53BF5B7AF75`');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BF5B7AF75');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT `FK_7656F53BF5B7AF75` FOREIGN KEY (address_id) REFERENCES address (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
