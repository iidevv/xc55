<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Logs
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Logs extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'logs/body.twig';
    }

    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), [
            'logs',
        ]);
    }

    protected function getData()
    {
        return $this->filterData(\XLite\View\FileManager::preparePath(LC_DIR_LOG));
    }

    protected function filterData($data)
    {
        $data = array_filter($data, static function ($element) {
            return $element['type'] == \XLite\View\FileManager::TYPE_DIR
                || preg_match(\XLite\Logger::LOG_FILE_NAME_PATTERN, str_replace(LC_DIR_LOG, '', $element['path']));
        });

        foreach ($data as &$element) {
            if (!empty($element['children'])) {
                $element['children'] = $this->filterData($element['children']);
            }
        }

        return $data;
    }

    protected function getLinkClosure()
    {
        return static function ($path, $mode = 'view') {
            $result = '';

            switch ($mode) {
                case 'view':
                    $result = \XLite\Logger::getLogPathAccessURL($path, 'view', true);
                    break;
                case 'download':
                    $result = \XLite\Logger::getLogPathAccessURL($path, 'download', true);
                    break;
            }
            return $result;
        };
    }
}
