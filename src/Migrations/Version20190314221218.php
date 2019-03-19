<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190314221218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wallet_pending_invitation (id INT AUTO_INCREMENT NOT NULL, wallet_id INT NOT NULL, user_id INT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_138AFB4C712520F3 (wallet_id), INDEX IDX_138AFB4CA76ED395 (user_id), UNIQUE INDEX UNIQ_138AFB4C712520F3A76ED395 (wallet_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, n_read TINYINT(1) NOT NULL, n_content VARCHAR(255) NOT NULL, n_title VARCHAR(255) NOT NULL, n_links JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wallet_pending_invitation ADD CONSTRAINT FK_138AFB4C712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet_pending_invitation ADD CONSTRAINT FK_138AFB4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE wallet_pending_invitation');
        $this->addSql('DROP TABLE notification');
    }
}
