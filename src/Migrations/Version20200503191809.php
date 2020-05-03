<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503191809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE shared_contact (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, shared_with_id INT NOT NULL, INDEX IDX_6DB5EA8FE7A1254A (contact_id), INDEX IDX_6DB5EA8FD14FE63F (shared_with_id), UNIQUE INDEX UNIQ_6DB5EA8FE7A1254AD14FE63F (contact_id, shared_with_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(32) NOT NULL, INDEX IDX_4C62E6387E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE shared_contact ADD CONSTRAINT FK_6DB5EA8FE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)'
        );
        $this->addSql(
            'ALTER TABLE shared_contact ADD CONSTRAINT FK_6DB5EA8FD14FE63F FOREIGN KEY (shared_with_id) REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE contact ADD CONSTRAINT FK_4C62E6387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE shared_contact DROP FOREIGN KEY FK_6DB5EA8FE7A1254A');
        $this->addSql('DROP TABLE shared_contact');
        $this->addSql('DROP TABLE contact');
    }
}
