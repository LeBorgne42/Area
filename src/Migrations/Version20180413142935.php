<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413142935 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet ADD sector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('CREATE INDEX IDX_68136AA5DE95C867 ON planet (sector_id)');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8A25E9820');
        $this->addSql('DROP INDEX UNIQ_4BA3D9E8A25E9820 ON sector');
        $this->addSql('ALTER TABLE sector DROP planet_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5DE95C867');
        $this->addSql('DROP INDEX IDX_68136AA5DE95C867 ON planet');
        $this->addSql('ALTER TABLE planet DROP sector_id');
        $this->addSql('ALTER TABLE sector ADD planet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4BA3D9E8A25E9820 ON sector (planet_id)');
    }
}
