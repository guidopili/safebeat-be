<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190315124047 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification ADD content VARCHAR(255) NOT NULL, ADD title VARCHAR(255) NOT NULL, DROP n_content, DROP n_title, CHANGE n_read is_read TINYINT(1) NOT NULL, CHANGE n_links links JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification ADD n_content VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD n_title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP content, DROP title, CHANGE is_read n_read TINYINT(1) NOT NULL, CHANGE links n_links JSON NOT NULL');
    }
}
