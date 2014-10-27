<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140615194752 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE invoice_products_returns (id INT AUTO_INCREMENT NOT NULL, invoice_product_id INT NOT NULL, return_reason_id INT NOT NULL, quantity INT NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_58026F7ABC5816C4 (invoice_product_id), INDEX IDX_58026F7AACA2AB22 (return_reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE invoice_products_returns ADD CONSTRAINT FK_58026F7ABC5816C4 FOREIGN KEY (invoice_product_id) REFERENCES invoice_products (id)");
        $this->addSql("ALTER TABLE invoice_products_returns ADD CONSTRAINT FK_58026F7AACA2AB22 FOREIGN KEY (return_reason_id) REFERENCES invoice_product_return_reason (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE invoice_products_returns");
    }
}
