<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206161104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, rang INT NOT NULL, resultat TINYINT NOT NULL, etape_id INT NOT NULL, envoi_id INT NOT NULL, INDEX IDX_47CC8C924A8CA2AD (etape_id), INDEX IDX_47CC8C923F97ECE5 (envoi_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C924A8CA2AD FOREIGN KEY (etape_id) REFERENCES etape (id)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C923F97ECE5 FOREIGN KEY (envoi_id) REFERENCES envoi (id)');
        $this->addSql('ALTER TABLE etape ADD statut_si_negatif_id INT NOT NULL');
        $this->addSql('ALTER TABLE etape ADD CONSTRAINT FK_285F75DD4DD787F1 FOREIGN KEY (statut_si_negatif_id) REFERENCES statut_envoi (id)');
        $this->addSql('CREATE INDEX IDX_285F75DD4DD787F1 ON etape (statut_si_negatif_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C924A8CA2AD');
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C923F97ECE5');
        $this->addSql('DROP TABLE action');
        $this->addSql('ALTER TABLE etape DROP FOREIGN KEY FK_285F75DD4DD787F1');
        $this->addSql('DROP INDEX IDX_285F75DD4DD787F1 ON etape');
        $this->addSql('ALTER TABLE etape DROP statut_si_negatif_id');
    }
}
