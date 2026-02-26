<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260226140256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE numero ADD envoi_id INT NOT NULL');
        $this->addSql('ALTER TABLE numero ADD CONSTRAINT FK_F55AE19E3F97ECE5 FOREIGN KEY (envoi_id) REFERENCES envoi (id)');
        $this->addSql('CREATE INDEX IDX_F55AE19E3F97ECE5 ON numero (envoi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE numero DROP FOREIGN KEY FK_F55AE19E3F97ECE5');
        $this->addSql('DROP INDEX IDX_F55AE19E3F97ECE5 ON numero');
        $this->addSql('ALTER TABLE numero DROP envoi_id');
    }
}
