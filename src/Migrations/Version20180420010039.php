<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420010039 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ally_war (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_BEBC3F951C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_proposal (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, user_id INT DEFAULT NULL, proposalAt DATETIME NOT NULL, INDEX IDX_D6F70BC11C6E3E76 (ally_id), INDEX IDX_D6F70BC1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_pna (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_B8D574011C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ally_war ADD CONSTRAINT FK_BEBC3F951C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE ally_proposal ADD CONSTRAINT FK_D6F70BC11C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE ally_proposal ADD CONSTRAINT FK_D6F70BC1A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE ally_pna ADD CONSTRAINT FK_B8D574011C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('DROP TABLE pna');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE war');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pna (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL COLLATE utf8mb4_unicode_ci, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_6A7BA6A51C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, user_id INT DEFAULT NULL, proposalAt DATETIME NOT NULL, INDEX IDX_BFE594721C6E3E76 (ally_id), INDEX IDX_BFE59472A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE war (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL COLLATE utf8mb4_unicode_ci, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_6C12ED311C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pna ADD CONSTRAINT FK_6A7BA6A51C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE war ADD CONSTRAINT FK_6C12ED311C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('DROP TABLE ally_war');
        $this->addSql('DROP TABLE ally_proposal');
        $this->addSql('DROP TABLE ally_pna');
    }
}
