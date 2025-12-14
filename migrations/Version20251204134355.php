<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204134355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649558d68ea');
        $this->addSql('DROP INDEX uniq_8d93d649558d68ea');
        $this->addSql('ALTER TABLE "user" DROP tokens_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD tokens_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649558d68ea FOREIGN KEY (tokens_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649558d68ea ON "user" (tokens_id)');
    }
}
