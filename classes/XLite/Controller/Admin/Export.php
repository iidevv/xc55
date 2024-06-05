<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Request;

class Export extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @var \XLite\Logic\Export\Generator
     */
    protected $generator;

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return static::t('Import & export');
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        if (
            Request::getInstance()->widget === 'XLite\View\PopupExport'
            || in_array($this->getAction(), ['itemlist_export', 'download'], true)
        ) {
            return true;
        }

        return parent::checkACL() || Auth::getInstance()->isPermissionAllowed('manage export');
    }

    /**
     * Get generator
     *
     * @return \XLite\Logic\Export\Generator
     */
    public function getGenerator()
    {
        if (!isset($this->generator)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

            $this->generator = ($state && isset($state['options']))
                ? new \XLite\Logic\Export\Generator($state['options'])
                : false;
        }

        return $this->generator;
    }

    /**
     * Get current page
     *
     * @return string
     */
    public function getPage()
    {
        if (!\XLite\Core\Request::getInstance()->page) {
            \XLite\Core\Request::getInstance()->page = 'new';
        }

        return \XLite\Core\Request::getInstance()->page;
    }

    /**
     * Get export state
     *
     * @return bool
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }

    protected function doActionExport()
    {
        foreach (\XLite\Core\Request::getInstance()->options as $key => $value) {
            if (
                !\XLite\Core\Config::getInstance()->Export
                || \XLite\Core\Config::getInstance()->Export->$key !== $value
            ) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    [
                        'category' => 'Export',
                        'name'     => $key,
                        'value'    => $value,
                    ]
                );
            }
        }

        if (in_array('XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox', \XLite\Core\Request::getInstance()->section)) {
            $addSections = [
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect',
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueText',
                'XLite\Logic\Export\Step\AttributeValues\AttributeValueHidden',
            ];

            \XLite\Core\Request::getInstance()->section = array_merge(
                \XLite\Core\Request::getInstance()->section,
                $addSections
            );
        }

        \XLite\Logic\Export\Generator::run($this->assembleExportOptions());
    }

    /**
     * Export action
     */
    protected function doActionItemlistExport()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        if ($state) {
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
        }

        \XLite\Logic\Export\Generator::run($this->assembleItemsListExportOptions());
        $this->setPureAction(true);
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleExportOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return [
            'include'       => $request->section,
            'copyResources' => \XLite\Core\Request::getInstance()->options['files'] === 'local',
            'attrs'         => $request->options['attrs'],
            'delimiter'     => $request->options['delimiter'] ?? \XLite\Core\Config::getInstance()->Units->csv_delim,
            'charset'       => $request->options['charset'] ?? \XLite\Core\Config::getInstance()->Units->export_import_charset,
            'filter'        => $request->options['filter'] ?? '',
            'selection'     => $request->options['selection'] ?? [],
        ];
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleItemsListExportOptions()
    {
        $options              = $this->assembleExportOptions();
        $options['itemsList'] = true;

        return $options;
    }

    protected function doActionCancel()
    {
        \XLite\Logic\Export\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('Export has been cancelled.');

        $this->setSilenceClose(true);
    }

    protected function doActionDownload()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
        $path  = \XLite\Core\Request::getInstance()->path;
        if ($state && $path) {
            $generator = new \XLite\Logic\Export\Generator($state['options']);
            $list      = $generator->getDownloadableFiles();

            $path = LC_DIR_VAR . $generator->getOptions()->dir . LC_DS . $path;
            if (in_array($path, $list, true)) {
                $name = basename($path);
                header('Content-Type: ' . $this->detectMimeType($path) . '; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
                header('Content-Length: ' . filesize($path));

                $this->set('silent', true);

                readfile($path);
                die(0);
            }
        }
    }

    /**
     * Delete all files
     */
    protected function doActionDeleteFiles()
    {
        $generator = new \XLite\Logic\Export\Generator();
        $generator->deleteAllFiles();

        $this->setReturnURL($this->buildURL('export'));
    }

    /**
     * Pack and download
     */
    protected function doActionPack()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
        $type  = \XLite\Core\Request::getInstance()->type;
        if ($state && $type) {
            $generator = new \XLite\Logic\Export\Generator($state['options']);
            $path      = $generator->packFiles($type);

            if ($path) {
                $name = basename($path);
                header('Content-Type: ' . $this->detectMimeType($path) . '; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
                header('Content-Length: ' . filesize($path));

                readfile($path);
                die(0);
            }
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['itemlist_export', 'pack', 'download', 'cancel']);
    }

    /**
     * Detect MIME type
     *
     * @param string $path File path
     *
     * @return string
     */
    protected function detectMimeType($path)
    {
        $type = 'application/octet-stream';

        if (preg_match('/\.csv$/Ss', $path)) {
            $type = 'text/csv';
        } elseif (class_exists('finfo', false)) {
            $fi   = new \finfo(FILEINFO_MIME);
            $type = $fi->file($path);
        }

        return $type;
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Export\Generator::getEventName();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getExportCancelFlagVarName()
    {
        return \XLite\Logic\Export\Generator::getCancelFlagVarName();
    }

    /**
     * @return string
     */
    public function printAJAXAttributes()
    {
        $result = parent::printAJAXAttributes();
        if ($this->isExportNotFinished()) {
            $result .= ' data-dialog-no-close="true"';
        }

        return $result;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return bool
     */
    protected function isExportNotFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && in_array((int) $state['state'], [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS], true)
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }
}
