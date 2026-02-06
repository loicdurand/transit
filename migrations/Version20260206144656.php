<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206144656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE envoi (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, destinataire_id INT NOT NULL, objet_id INT NOT NULL, INDEX IDX_CA7E3566A4F84F6E (destinataire_id), INDEX IDX_CA7E3566F520CF5A (objet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE objet (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(25) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES destinataire (id)');
        $this->addSql('ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566F520CF5A FOREIGN KEY (objet_id) REFERENCES objet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566A4F84F6E');
        $this->addSql('ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566F520CF5A');
        $this->addSql('DROP TABLE envoi');
        $this->addSql('DROP TABLE objet');
    }
}
