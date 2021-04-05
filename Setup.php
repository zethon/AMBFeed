<?php

namespace lulzapps\Feed;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends \XF\AddOn\AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $this->schemaManager()->createTable('lulzapps_feed_entry', 
            function(Create $table)
            {
                $table->addColumn('entry_id', 'int');
                $table->addColumn('user_id', 'int');
                $table->addColumn('comment', 'varchar', 255)->setDefault('');
                $table->addColumn('date', 'int');
                $table->addColumn('reply_to', 'int');
                $table->addColumn('deleted', 'bool');
                $table->addPrimaryKey('entry_id');
            });
    }

    public function installStep2()
    {
        $this->schemaManager()->createTable('lulzapps_feed_reaction', 
            function(Create $table)
            {
                $table->addColumn('entry_reaction_id', 'int');
                $table->addColumn('entry_id', 'int');
                $table->addColumn('user_id', 'int');
                $table->addColumn('date', 'int');
                $table->addColumn('reaction', 'varchar', 8)->setDefault('');
                $table->addPrimaryKey('entry_reaction_id');
            });
    }

    public function installStep3()
    {
        $this->schemaManager()->createTable('lulzapps_feed_entry_deleted', 
            function(Create $table)
            {
                $table->addColumn('entry_deleted_id', 'int');
                $table->addColumn('entry_id', 'int');
                $table->addColumn('user_id', 'int');
                $table->addColumn('date', 'int');
                $table->addColumn('reason', 'varchar', 120)->setDefault('');
                $table->addPrimaryKey('entry_deleted_id');
            });
    }
}