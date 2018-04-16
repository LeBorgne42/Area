<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416123641 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fleet (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE soldier ADD fleet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE soldier ADD CONSTRAINT FK_B04F2D024B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B04F2D024B061DF9 ON soldier (fleet_id)');
        $this->addSql('ALTER TABLE scientist DROP INDEX IDX_E1774A61A25E9820, ADD UNIQUE INDEX UNIQ_E1774A61A25E9820 (planet_id)');
        $this->addSql('ALTER TABLE scientist ADD fleet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A614B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1774A614B061DF9 ON scientist (fleet_id)');
        $this->addSql('ALTER TABLE worker DROP INDEX IDX_9FB2BF62A25E9820, ADD UNIQUE INDEX UNIQ_9FB2BF62A25E9820 (planet_id)');
        $this->addSql('ALTER TABLE worker ADD fleet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF624B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9FB2BF624B061DF9 ON worker (fleet_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE soldier DROP FOREIGN KEY FK_B04F2D024B061DF9');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A614B061DF9');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF624B061DF9');
        $this->addSql('DROP TABLE fleet');
        $this->addSql('ALTER TABLE scientist DROP INDEX UNIQ_E1774A61A25E9820, ADD INDEX IDX_E1774A61A25E9820 (planet_id)');
        $this->addSql('DROP INDEX UNIQ_E1774A614B061DF9 ON scientist');
        $this->addSql('ALTER TABLE scientist DROP fleet_id');
        $this->addSql('DROP INDEX UNIQ_B04F2D024B061DF9 ON soldier');
        $this->addSql('ALTER TABLE soldier DROP fleet_id');
        $this->addSql('ALTER TABLE worker DROP INDEX UNIQ_9FB2BF62A25E9820, ADD INDEX IDX_9FB2BF62A25E9820 (planet_id)');
        $this->addSql('DROP INDEX UNIQ_9FB2BF624B061DF9 ON worker');
        $this->addSql('ALTER TABLE worker DROP fleet_id');
    }
}
