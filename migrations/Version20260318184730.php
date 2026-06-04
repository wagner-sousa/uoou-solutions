<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260318184730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('products');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('image', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('price', 'float');
        $table->addColumn('stock_quantity', 'float');
        $table->setPrimaryKey(['id']);

        $table = $schema->createTable('messenger_messages');
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('body', 'text');
        $table->addColumn('headers', 'text');
        $table->addColumn('queue_name', 'string', ['length' => 190]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('available_at', 'datetime');
        $table->addColumn('delivered_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['queue_name', 'available_at', 'delivered_at', 'id'], 'IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('products');
        $schema->dropTable('messenger_messages');
    }
}
