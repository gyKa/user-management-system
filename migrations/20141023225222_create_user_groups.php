<?php

use Phinx\Migration\AbstractMigration;

class CreateUserGroups extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     */
    public function change()
    {
        $table = $this->table('user_groups');
        $table->addColumn('user_id', 'integer');
        $table->addColumn('group_id', 'integer')
              ->create();

        $table = $this->table('user_groups');
        $table->addForeignKey('user_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'RESTRICT']);
        $table->addForeignKey('group_id', 'groups', 'id', ['delete'=> 'RESTRICT', 'update'=> 'RESTRICT']);
        $table->addIndex(['user_id', 'group_id'], ['unique' => true])
              ->save();
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
