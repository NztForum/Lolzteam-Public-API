<?php

namespace Xfrocks\Api;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\AddOn\StepRunnerUninstallTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    /**
     * @return void
     */
    public function installStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $closure) {
            $sm->createTable($tableName, $closure);
        }
    }

    /**
     * @return void
     */
    public function uninstallStep1()
    {
        $sm = $this->schemaManager();

        foreach (array_keys($this->getTables()) as $tableName) {
            $sm->dropTable($tableName);
        }
    }

    /**
     * @return void
     */
    public function upgrade2000012Step1()
    {
        $sm = $this->schemaManager();

        $sm->alterTable('xf_bdapi_auth_code', function (Alter $table) {
            $table->changeColumn('client_id', 'varbinary')->length(255);
            $table->changeColumn('auth_code_text', 'varbinary')->length(255);
        });

        $sm->alterTable('xf_bdapi_client', function (Alter $table) {
            $table->changeColumn('client_id', 'varbinary')->length(255);
            $table->changeColumn('client_secret', 'varbinary')->length(255);
            $table->changeColumn('name', 'text');

            $table->convertCharset('utf8mb4');
        });

        $sm->alterTable('xf_bdapi_refresh_token', function (Alter $table) {
            $table->changeColumn('client_id', 'varbinary')->length(255);
            $table->changeColumn('refresh_token_text', 'varbinary')->length(255);
            $table->changeColumn('scope', 'blob');
        });

        $sm->alterTable('xf_bdapi_token', function (Alter $table) {
            $table->changeColumn('client_id', 'varbinary')->length(255);
            $table->changeColumn('token_text', 'varbinary')->length(255);
            $table->changeColumn('scope', 'blob');
        });
    }

    /**
     * @return void
     */
    public function upgrade2000013Step1()
    {
        $sm = $this->schemaManager();

        $sm->alterTable('xf_bdapi_auth_code', function (Alter $table) {
            $table->addIndex()->type('key')->columns('expire_date');
        });

        $sm->alterTable('xf_bdapi_client', function (Alter $table) {
            $table->addIndex()->type('key')->columns('user_id');
        });

        $sm->alterTable('xf_bdapi_refresh_token', function (Alter $table) {
            $table->addIndex()->type('key')->columns('expire_date');
        });

        $sm->alterTable('xf_bdapi_token', function (Alter $table) {
            $table->addIndex()->type('key')->columns('client_id');
            $table->addIndex()->type('key')->columns('expire_date');
            $table->addIndex()->type('key')->columns('user_id');
        });
    }

    /**
     * @return void
     */
    public function upgrade2000014Step1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables2() as $tableName => $closure) {
            $sm->createTable($tableName, $closure);
        }

        $sm->alterTable('xf_bdapi_user_scope', function (Alter $table) {
            $table->changeColumn('client_id', 'varbinary')->length(255);
            $table->changeColumn('scope', 'varbinary')->length(255);

            $table->addIndex()->type('unique')->columns(['client_id', 'user_id', 'scope']);
        });
    }

    /**
     * @return void
     */
    public function upgrade2000015Step1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables3() as $tableName => $closure) {
            $sm->createTable($tableName, $closure);
        }
    }

    public function upgrade2000135Step1()
    {
        $this->schemaManager()->alterTable('xf_bdapi_token', function (Alter $table) {
            $table->addColumn('issue_date', 'int')->setDefault(0);
        });
    }

    /**
     * @return array
     */
    private function getTables()
    {
        $tables = [];

        $tables += $this->getTables1();
        $tables += $this->getTables2();
        $tables += $this->getTables3();

        return $tables;
    }

    /**
     * @return array
     */
    private function getTables1()
    {
        $tables = [];

        $tables['xf_bdapi_auth_code'] = function (Create $table) {
            $table->addColumn('auth_code_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('auth_code_text', 'varbinary')->length(255);
            $table->addColumn('redirect_uri', 'text');
            $table->addColumn('expire_date', 'int');
            $table->addColumn('user_id', 'int');
            $table->addColumn('scope', 'text');

            $table->addUniqueKey('auth_code_text');
        };

        $tables['xf_bdapi_client'] = function (Create $table) {
            $table->addColumn('client_id', 'varbinary')->length(255)->primaryKey();
            $table->addColumn('client_secret', 'varbinary')->length(255);
            $table->addColumn('redirect_uri', 'text');
            $table->addColumn('name', 'text');
            $table->addColumn('description', 'text');
            $table->addColumn('user_id', 'int');
            $table->addColumn('options', 'mediumblob')->nullable(true);
        };

        $tables['xf_bdapi_refresh_token'] = function (Create $table) {
            $table->addColumn('refresh_token_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('refresh_token_text', 'varbinary')->length(255);
            $table->addColumn('expire_date', 'int');
            $table->addColumn('user_id', 'int');
            $table->addColumn('scope', 'blob');

            $table->addUniqueKey('refresh_token_text');
        };

        $tables['xf_bdapi_token'] = function (Create $table) {
            $table->addColumn('token_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('token_text', 'varbinary')->length(255);
            $table->addColumn('expire_date', 'int');
            $table->addColumn('user_id', 'int');
            $table->addColumn('scope', 'blob');
            $table->addColumn('issue_date', 'int')->setDefault(0);

            $table->addUniqueKey('token_text');
        };

        return $tables;
    }

    /**
     * @return array
     */
    private function getTables2()
    {
        $tables = [];

        $tables['xf_bdapi_user_scope'] = function (Create $table) {
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('user_id', 'int');
            $table->addColumn('scope', 'varbinary')->length(255);
            $table->addColumn('accept_date', 'int');

            $table->addKey('user_id');
            $table->addUniqueKey(['client_id', 'user_id', 'scope']);
        };

        return $tables;
    }

    /**
     * @return array
     */
    private function getTables3()
    {
        $tables = [];

        $tables['xf_bdapi_subscription'] = function (Create $table) {
            $table->addColumn('subscription_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('callback', 'text');
            $table->addColumn('topic', 'varbinary')->length(255);
            $table->addColumn('subscribe_date', 'int')->unsigned();
            $table->addColumn('expire_date', 'int')->unsigned()->setDefault(0);

            $table->addKey('client_id');
            $table->addKey('topic');
        };

        $tables['xf_bdapi_log'] = function (Create $table) {
            $table->addColumn('log_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('client_id', 'varbinary')->length(255);
            $table->addColumn('user_id', 'int')->unsigned();
            $table->addColumn('ip_address', 'varbinary')->length(50);
            $table->addColumn('request_date', 'int')->unsigned();
            $table->addColumn('request_method', 'varbinary')->length(10);
            $table->addColumn('request_uri', 'text');
            $table->addColumn('request_data', 'mediumblob');
            $table->addColumn('response_code', 'int')->unsigned();
            $table->addColumn('response_output', 'mediumblob');
        };

        $tables['xf_bdapi_ping_queue'] = function (Create $table) {
            $table->addColumn('ping_queue_id', 'int')->autoIncrement()->primaryKey();
            $table->addColumn('callback_md5', 'varbinary')->length(32);
            $table->addColumn('callback', 'text');
            $table->addColumn('object_type', 'varbinary', 25);
            $table->addColumn('data', 'mediumblob');
            $table->addColumn('queue_date', 'int')->unsigned();
            $table->addColumn('expire_date', 'int')->unsigned()->setDefault(0);

            $table->addKey('callback_md5');
        };

        return $tables;
    }
}
