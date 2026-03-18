<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260318200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Stripe price identifier to products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE products ADD stripe_price_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE products DROP stripe_price_id');
    }
}
