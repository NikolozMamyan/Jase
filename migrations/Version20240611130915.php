<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611130915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like_tables` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, feed_id INT NOT NULL, INDEX IDX_BA9E11D3A76ED395 (user_id), INDEX IDX_BA9E11D351A5BC03 (feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `like_tables` ADD CONSTRAINT FK_BA9E11D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `like_tables` ADD CONSTRAINT FK_BA9E11D351A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B351A5BC03');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('DROP TABLE `like`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, feed_id INT NOT NULL, INDEX IDX_AC6340B351A5BC03 (feed_id), INDEX IDX_AC6340B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B351A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like_tables` DROP FOREIGN KEY FK_BA9E11D3A76ED395');
        $this->addSql('ALTER TABLE `like_tables` DROP FOREIGN KEY FK_BA9E11D351A5BC03');
        $this->addSql('DROP TABLE `like_tables`');
    }
}
