<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206155618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type_envoi (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(10) NOT NULL, maximum INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE envoi ADD titre VARCHAR(25) NOT NULL, ADD reference VARCHAR(50) DEFAULT NULL, ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566C54C8C93 FOREIGN KEY (type_id) REFERENCES type_envoi (id)');
        $this->addSql('CREATE INDEX IDX_CA7E3566C54C8C93 ON envoi (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE type_envoi');
        $this->addSql('ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566C54C8C93');
        $this->addSql('DROP INDEX IDX_CA7E3566C54C8C93 ON envoi');
        $this->addSql('ALTER TABLE envoi DROP titre, DROP reference, DROP type_id');
    }
}
