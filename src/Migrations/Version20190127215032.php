<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127215032 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE money_transaction (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, wallet_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(80) NOT NULL, INDEX IDX_D21254E212469DE2 (category_id), INDEX IDX_D21254E27E3C61F9 (owner_id), INDEX IDX_D21254E2712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(25) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE money_transaction ADD CONSTRAINT FK_D21254E212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE money_transaction ADD CONSTRAINT FK_D21254E27E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE money_transaction ADD CONSTRAINT FK_D21254E2712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C17E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE money_transaction DROP FOREIGN KEY FK_D21254E212469DE2');
        $this->addSql('DROP TABLE money_transaction');
        $this->addSql('DROP TABLE category');
    }
}
