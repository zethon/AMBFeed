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
                $table->addPrimaryKey('entry_id');
            });
    }
}