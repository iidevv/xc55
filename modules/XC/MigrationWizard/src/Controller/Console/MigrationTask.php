<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Controller\Console;

/**
 * Event task controller
 */
class MigrationTask extends \XLite\Controller\Console\AConsole
{
    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
    }

    /**
     * Run task
     *
     * @return void
     */
    protected function doActionRun()
    {
        $event = 'migration';

        define('MIGRATION_WIZARD_CHUNK', 100);
        \Includes\Utils\FileManager::deleteFile(LC_DIR_VAR . '.migrationStop');
        \Includes\Utils\FileManager::deleteFile(LC_DIR_VAR . '.migrationStatus');

        do {
            if (\Includes\Utils\FileManager::isFileReadable(LC_DIR_VAR . '.migrationStop')) {
                $this->putMigrationStatusData(['stopped']);
                break;
            }

            if (\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventStatePercent($event) == 100) {
                $this->putMigrationStatusData(['complete']);
                break;
            }

            \XLite\Core\Database::getEM()->clear();

            $result = false;
            $errors = [];

            $handleTime = 0;
            $task = \XLite\Core\Database::getRepo('XLite\Model\EventTask')->findOneBy(['name' => $event]);
            if ($task) {
                \XLite\Core\Database::getRepo('XLite\Model\EventTask')->cleanTasks($event, $task->getId());
                $handleStartTime = microtime(true);

                $this->resetData();
                if (\XLite\Core\EventListener::getInstance()->handle($task->getName(), $task->getArguments())) {
                    $task = \XLite\Core\Database::getEM()->merge($task);
                    \XLite\Core\Database::getEM()->remove($task);
                    $result = true;
                }
                $handleTime = microtime(true) - $handleStartTime;
                $errors = \XLite\Core\EventListener::getInstance()->getErrors();
            } else {
                \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($event);
            }

            \XLite\Core\Database::getEM()->flush();

            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($event);

            if ($result && $state) {
                $data = [
                    'percent' => \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventStatePercent($event),
                    'error' => !empty($errors),
                    'processing time' => $handleTime,
                ];

                if (!empty($state['touchData'])) {
                    $data += $state['touchData'];
                }

                if ($data['percent'] == 100) {
                    $result = false;
                }

                echo sprintf("\r%s %s%% done. Processing time: %s", isset($data['message']) ? ($data['message'] . '. ') : '', $data['percent'], $data['processing time']);

                $this->putMigrationStatusData($data);
            } else {
                $result = false;
            }

            if ($errors) {
                print_r($errors);

                $result = false;
            }
        } while ($result);
    }

    /**
     * Run task
     *
     * @return void
     */
    protected function doActionTouch()
    {
        $event = 'migration';
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($event);

        $data = [
            'percent' => \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventStatePercent($event),
            'error'   => false,
        ];

        if (!empty($state['touchData'])) {
            $data += $state['touchData'];
        }

        print (json_encode($data));
    }

    protected function doActionFillData()
    {
        if (\XLite\Core\TmpVars::getInstance()->{\XC\MigrationWizard\Logic\Migration\Wizard::CELL_NAME}) {
            return;
        }

        \XLite\Core\Auth::getInstance()->closeStorefront();

        $wizard = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance();
        \XLite\Core\Request::getInstance()->agree = 1;
        $wizard->doStart();
        $connect = $wizard->getLastStep();

        $databaseDetails = \XLite::getInstance()->getOptions('database_details');
        $databaseName = $databaseDetails['database'];

        $connectData = [
            'username' => $databaseDetails['username'],
            'password' => $databaseDetails['password'],
            'database' => substr($databaseName, 0, strlen($databaseName) - 3) . 'xc4',
            'host' => 'localhost',
            'port' => '',
            'socket' => '',
            'prefix' => 'xcart_',
            'secret' => '123',
            'url' => '',
            'path' => LC_DIR . LC_DS . 'xc4/',
        ];

        $request = \XLite\Core\Request::getInstance();
        foreach ($connectData as $key => $data) {
            $request->__set('mw_' . $key, $data);
        }
        $connect->saveData();

        $pages = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->findAll();
        foreach ($pages as $page) {
            \XLite\Core\Database::getEM()->remove($page);
        }
        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'name' => 'timestamp',
            'category' => 'Version',
            'value' => time(),
        ]);
        \XLite\Core\Config::updateInstance();

        if ($request->currencyCode) {
            $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->findOneBy(['code' => strtoupper($request->currencyCode)]);

            if ($currency) {
                $shopCurrency = \XLite\Core\Database::getRepo('XLite\Model\Config')
                    ->findOneBy(['name' => 'shop_currency', 'category' => 'General']);

                \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                    $shopCurrency,
                    ['value' => $currency->getCurrencyId()]
                );
            }
        }
    }

    protected function resetData()
    {
        \XC\MigrationWizard\Core\EventListener\Migration::resetInstance();
        \XLite::getInstance()->resetCurrency();
    }

    protected function putMigrationStatusData($data)
    {
        file_put_contents(LC_DIR_VAR . '.migrationStatus', json_encode($data));
    }
}
