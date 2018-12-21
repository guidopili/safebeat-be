<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181220161506 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wallet ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_7C68921F7E3C61F9 ON wallet (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F7E3C61F9');
        $this->addSql('DROP INDEX IDX_7C68921F7E3C61F9 ON wallet');
        $this->addSql('ALTER TABLE wallet DROP owner_id');
    }
}
