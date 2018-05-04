<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504005257 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_content (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message VARCHAR(500) NOT NULL, bitcoin BIGINT NOT NULL, sendAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_C088E503A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salon (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_F268F4171C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salon_user (salon_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6B3C75974C91BDE4 (salon_id), INDEX IDX_6B3C7597A76ED395 (user_id), PRIMARY KEY(salon_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_content ADD CONSTRAINT FK_C088E503A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE salon ADD CONSTRAINT FK_F268F4171C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE salon_user ADD CONSTRAINT FK_6B3C75974C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salon_user ADD CONSTRAINT FK_6B3C7597A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE salon_user DROP FOREIGN KEY FK_6B3C75974C91BDE4');
        $this->addSql('DROP TABLE s_content');
        $this->addSql('DROP TABLE salon');
        $this->addSql('DROP TABLE salon_user');
    }
}
