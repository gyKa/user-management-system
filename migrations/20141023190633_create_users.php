<?php

use Phinx\Migration\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string', ['limit' => 100])
              ->create();
    }
    
    /**
     * Migrate Up.
     */
    public function up()
    {
    
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
