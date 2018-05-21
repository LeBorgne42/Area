<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180519125426 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE salon_ally (salon_id INT NOT NULL, ally_id INT NOT NULL, INDEX IDX_E52D33D34C91BDE4 (salon_id), INDEX IDX_E52D33D31C6E3E76 (ally_id), PRIMARY KEY(salon_id, ally_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE salon_ally ADD CONSTRAINT FK_E52D33D34C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salon_ally ADD CONSTRAINT FK_E52D33D31C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salon DROP FOREIGN KEY FK_F268F4171C6E3E76');
        $this->addSql('DROP INDEX UNIQ_F268F4171C6E3E76 ON salon');
        $this->addSql('ALTER TABLE salon DROP ally_id, CHANGE name name VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE salon_ally');
        $this->addSql('ALTER TABLE salon ADD ally_id INT DEFAULT NULL, CHANGE name name VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE salon ADD CONSTRAINT FK_F268F4171C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F268F4171C6E3E76 ON salon (ally_id)');
    }
}
