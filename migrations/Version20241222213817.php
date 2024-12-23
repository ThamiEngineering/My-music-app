<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222213817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC4D063B47');
        $this->addSql('DROP INDEX IDX_67F068BC4D063B47 ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE paylist_id playlist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC6BBD148 ON commentaire (playlist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6BBD148');
        $this->addSql('DROP INDEX IDX_67F068BC6BBD148 ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE playlist_id paylist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC4D063B47 FOREIGN KEY (paylist_id) REFERENCES playlist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_67F068BC4D063B47 ON commentaire (paylist_id)');
    }
}
