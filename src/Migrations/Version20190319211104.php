<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190319211104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE money_transaction (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, wallet_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(80) NOT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_D21254E2712520F3 (wallet_id), INDEX IDX_D21254E27E3C61F9 (owner_id), INDEX IDX_D21254E212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, target_user_id INT DEFAULT NULL, is_read TINYINT(1) NOT NULL, links JSON NOT NULL, content VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_BF5476CA6C066AFE (target_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, refresh_token VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, password VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, username VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:simple_array)\', email VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, first_name VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, last_name VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, title VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_7C68921F2B36786B (title), INDEX IDX_7C68921F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wallet_invited_user (wallet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FCD4106DA76ED395 (user_id), INDEX IDX_FCD4106D712520F3 (wallet_id), PRIMARY KEY(wallet_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wallet_pending_invitation (id INT AUTO_INCREMENT NOT NULL, wallet_id INT NOT NULL, user_id INT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_138AFB4CA76ED395 (user_id), UNIQUE INDEX UNIQ_138AFB4C712520F3A76ED395 (wallet_id, user_id), INDEX IDX_138AFB4C712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE money_transaction');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_invited_user');
        $this->addSql('DROP TABLE wallet_pending_invitation');
    }
}
