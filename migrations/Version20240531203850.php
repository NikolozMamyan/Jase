<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531203850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C51A5BC03');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B ON comment');
        $this->addSql('DROP INDEX IDX_9474526C51A5BC03 ON comment');
        $this->addSql('ALTER TABLE comment ADD comments_id INT DEFAULT NULL, ADD user_commented_id INT DEFAULT NULL, DROP feed_id, DROP author_id, DROP content, DROP created_at');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C63379586 FOREIGN KEY (comments_id) REFERENCES feed (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C416BDDA3 FOREIGN KEY (user_commented_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526C63379586 ON comment (comments_id)');
        $this->addSql('CREATE INDEX IDX_9474526C416BDDA3 ON comment (user_commented_id)');
        $this->addSql('ALTER TABLE feed CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C63379586');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C416BDDA3');
        $this->addSql('DROP INDEX IDX_9474526C63379586 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C416BDDA3 ON comment');
        $this->addSql('ALTER TABLE comment ADD feed_id INT NOT NULL, ADD author_id INT NOT NULL, ADD content LONGTEXT NOT NULL, ADD created_at DATETIME NOT NULL, DROP comments_id, DROP user_commented_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C51A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C51A5BC03 ON comment (feed_id)');
        $this->addSql('ALTER TABLE feed CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
