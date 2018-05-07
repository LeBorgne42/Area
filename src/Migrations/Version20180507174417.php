<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507174417 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet ADD cargoI BIGINT DEFAULT NULL, ADD cargoV BIGINT DEFAULT NULL, ADD cargoX BIGINT DEFAULT NULL, ADD hunterHeavy BIGINT DEFAULT NULL, ADD corvet BIGINT DEFAULT NULL, ADD corvetLaser BIGINT DEFAULT NULL, ADD fregatePlasma BIGINT DEFAULT NULL, ADD croiser BIGINT DEFAULT NULL, ADD ironClad BIGINT DEFAULT NULL, ADD destroyer BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD cargoI BIGINT DEFAULT NULL, ADD cargoV BIGINT DEFAULT NULL, ADD cargoX BIGINT DEFAULT NULL, ADD hunterHeavy BIGINT DEFAULT NULL, ADD corvet BIGINT DEFAULT NULL, ADD corvetLaser BIGINT DEFAULT NULL, ADD fregatePlasma BIGINT DEFAULT NULL, ADD croiser BIGINT DEFAULT NULL, ADD ironClad BIGINT DEFAULT NULL, ADD destroyer BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP cargoI, DROP cargoV, DROP cargoX, DROP hunterHeavy, DROP corvet, DROP corvetLaser, DROP fregatePlasma, DROP croiser, DROP ironClad, DROP destroyer');
        $this->addSql('ALTER TABLE planet DROP cargoI, DROP cargoV, DROP cargoX, DROP hunterHeavy, DROP corvet, DROP corvetLaser, DROP fregatePlasma, DROP croiser, DROP ironClad, DROP destroyer');
    }
}
