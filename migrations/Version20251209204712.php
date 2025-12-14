<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209204712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526ce85f12b8');
        $this->addSql('DROP INDEX idx_9474526ce85f12b8');
        $this->addSql('ALTER TABLE comment RENAME COLUMN post_id_id TO post_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C4B89032C');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment RENAME COLUMN post_id TO post_id_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526ce85f12b8 FOREIGN KEY (post_id_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9474526ce85f12b8 ON comment (post_id_id)');
    }
}
