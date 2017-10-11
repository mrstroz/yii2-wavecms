<?php

use yii\db\Migration;

class m170720_135952_alter_user_table extends Migration
{
    public function safeUp()
    {

        $this->addColumn('{{%user}}' , 'first_name', $this->string()->after('email'));
        $this->addColumn('{{%user}}' , 'last_name', $this->string()->after('first_name'));
        $this->addColumn('{{%user}}' , 'is_admin', $this->boolean()->after('status'));
        $this->addColumn('{{%user}}' , 'lang', $this->string()->after('is_admin'));

    }

    public function safeDown()
    {
        echo "m170720_135952_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170720_135952_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
