<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413142052 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE galaxy DROP FOREIGN KEY FK_F6BB1376A25E9820');
        $this->addSql('DROP INDEX UNIQ_F6BB1376A25E9820 ON galaxy');
        $this->addSql('ALTER TABLE galaxy DROP planet_id');
        $this->addSql('ALTER TABLE sector ADD galaxy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
        $this->addSql('CREATE INDEX IDX_4BA3D9E8B61FAB2 ON sector (galaxy_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE galaxy ADD planet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE galaxy ADD CONSTRAINT FK_F6BB1376A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6BB1376A25E9820 ON galaxy (planet_id)');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8B61FAB2');
        $this->addSql('DROP INDEX IDX_4BA3D9E8B61FAB2 ON sector');
        $this->addSql('ALTER TABLE sector DROP galaxy_id');
    }
}
