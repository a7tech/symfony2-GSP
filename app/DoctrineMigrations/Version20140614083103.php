<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140614083103 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE invoice_product_return_reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_stock_increased TINYINT(1) DEFAULT '0' NOT NULL, is_stock_blocked TINYINT(1) DEFAULT '0' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE invoice_products CHANGE quantity quantity INT NOT NULL, CHANGE price price DOUBLE PRECISION NOT NULL");
        $this->addSql("ALTER TABLE invoices ADD correction_of_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F95FE3DD8A9 FOREIGN KEY (correction_of_id) REFERENCES invoices (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_6A2F2F95FE3DD8A9 ON invoices (correction_of_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE invoice_product_return_reason");
        $this->addSql("ALTER TABLE invoice_products CHANGE quantity quantity INT DEFAULT NULL, CHANGE price price DOUBLE PRECISION DEFAULT NULL");
        $this->addSql("ALTER TABLE invoices DROP FOREIGN KEY FK_6A2F2F95FE3DD8A9");
        $this->addSql("DROP INDEX UNIQ_6A2F2F95FE3DD8A9 ON invoices");
        $this->addSql("ALTER TABLE invoices DROP correction_of_id");
    }
}
