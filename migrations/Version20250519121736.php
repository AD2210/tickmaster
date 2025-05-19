<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519121736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ticket_history (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, user_id INT NOT NULL, field VARCHAR(50) DEFAULT NULL, old_value LONGTEXT DEFAULT NULL, new_value LONGTEXT DEFAULT NULL, changed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_2B762919700047D2 (ticket_id), INDEX IDX_2B762919A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket_history ADD CONSTRAINT FK_2B762919700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket_history ADD CONSTRAINT FK_2B762919A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket_history DROP FOREIGN KEY FK_2B762919700047D2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket_history DROP FOREIGN KEY FK_2B762919A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ticket_history
        SQL);
    }
}
