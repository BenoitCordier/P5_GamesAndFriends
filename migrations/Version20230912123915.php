<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912123915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA761D870AA');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA761D870AA FOREIGN KEY (event_game_id) REFERENCES game (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD user_location LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA761D870AA');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA761D870AA FOREIGN KEY (event_game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user DROP user_location');
    }
}
