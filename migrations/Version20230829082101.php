<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829082101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, message_from_id INT NOT NULL, message_to_id INT NOT NULL, message_title VARCHAR(255) DEFAULT NULL, message_send_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', message_text LONGTEXT NOT NULL, INDEX IDX_B6BD307FE846E307 (message_from_id), INDEX IDX_B6BD307FCF93B58E (message_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE846E307 FOREIGN KEY (message_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCF93B58E FOREIGN KEY (message_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD event_max_player INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE846E307');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCF93B58E');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE event DROP event_max_player');
    }
}
