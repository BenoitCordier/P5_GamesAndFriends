<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009092014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_thread (id INT AUTO_INCREMENT NOT NULL, first_user_id INT NOT NULL, second_user_id INT NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_607D18CB4E2BF69 (first_user_id), INDEX IDX_607D18CB02C53F8 (second_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_thread ADD CONSTRAINT FK_607D18CB4E2BF69 FOREIGN KEY (first_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_thread ADD CONSTRAINT FK_607D18CB02C53F8 FOREIGN KEY (second_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD message_thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8829462F FOREIGN KEY (message_thread_id) REFERENCES message_thread (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F8829462F ON message (message_thread_id)');
        $this->addSql('ALTER TABLE `event` CHANGE `event_name` `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;');
        $this->addSql('ALTER TABLE `event` CHANGE `event_location` `location` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        $this->addSql('ALTER TABLE `event` CHANGE `user_name` `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;');
        $this->addSql('ALTER TABLE `event` CHANGE `user_location` `location` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8829462F');
        $this->addSql('ALTER TABLE message_thread DROP FOREIGN KEY FK_607D18CB4E2BF69');
        $this->addSql('ALTER TABLE message_thread DROP FOREIGN KEY FK_607D18CB02C53F8');
        $this->addSql('DROP TABLE message_thread');
        $this->addSql('DROP INDEX IDX_B6BD307F8829462F ON message');
        $this->addSql('ALTER TABLE message DROP message_thread_id');
    }
}
