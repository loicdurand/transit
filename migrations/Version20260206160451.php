<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206160451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statut_envoi (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(25) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE envoi ADD statut_id INT NOT NULL');
        $this->addSql('ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566F6203804 FOREIGN KEY (statut_id) REFERENCES statut_envoi (id)');
        $this->addSql('CREATE INDEX IDX_CA7E3566F6203804 ON envoi (statut_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE statut_envoi');
        $this->addSql('ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566F6203804');
        $this->addSql('DROP INDEX IDX_CA7E3566F6203804 ON envoi');
        $this->addSql('ALTER TABLE envoi DROP statut_id');
    }
}
