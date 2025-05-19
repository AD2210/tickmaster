<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519132150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, comment_id INT NOT NULL, filename VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_795FD9BBF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BBF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BBF8697D13
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE attachment
        SQL);
    }
}
