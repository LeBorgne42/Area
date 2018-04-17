<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416234904 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, user_id INT DEFAULT NULL, proposalAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_BFE594721C6E3E76 (ally_id), UNIQUE INDEX UNIQ_BFE59472A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('DROP TABLE user_ally');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_ally (user_id INT NOT NULL, ally_id INT NOT NULL, INDEX IDX_7903DCC4A76ED395 (user_id), INDEX IDX_7903DCC41C6E3E76 (ally_id), PRIMARY KEY(user_id, ally_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_ally ADD CONSTRAINT FK_7903DCC41C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ally ADD CONSTRAINT FK_7903DCC4A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE proposal');
    }
}
