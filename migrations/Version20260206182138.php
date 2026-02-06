<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206182138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD objet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92F520CF5A FOREIGN KEY (objet_id) REFERENCES objet (id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92F520CF5A ON action (objet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92F520CF5A');
        $this->addSql('DROP INDEX IDX_47CC8C92F520CF5A ON action');
        $this->addSql('ALTER TABLE action DROP objet_id');
    }
}
