<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Controller\Admin;

use XLite\Core\Request;
use CDev\SimpleCMS\View\FormField\Select\Select2\Pages;

class SearchPages extends \XLite\Controller\Admin\AAdmin
{
    protected function doNoAction()
    {
        $request = Request::getInstance();
        $pages   = Pages::getAllPages();
        $result  = [
            'pages' => [],
            'more' => false,
            'mode' => $request->path ? 'path' : ($request->search ? 'search' : 'default')
        ];
        $currentPathId = array_search($request->path, Pages::getAllPages()) ?: null;

        if ($request->search) {
            $pages = array_filter($pages, static function ($v) use ($request) {
                $parts = explode(Pages::$PATH_SEPARATOR, $v);
                $name  = array_pop($parts);
                return strpos(strtolower($name), strtolower($request->search)) !== false;
            });
        } elseif ($request->path) {
            $level = count(explode(Pages::$PATH_SEPARATOR, $request->path)) + 1;
            $pages = array_filter($pages, static function ($v) use ($request, $level) {
                $path  = strtolower($request->path);
                $item  = strtolower($v);
                $parts = explode(Pages::$PATH_SEPARATOR, $v);
                return strpos($item, $path) !== false
                    && $item !== $path
                    && count($parts) == $level;
            });
        } else {
            $pages = array_filter($pages, static function ($v) {
                $parts = explode(Pages::$PATH_SEPARATOR, $v);
                return count($parts) === 1;
            });
        }

        foreach ($pages as $id => $name) {
            $parts = explode(Pages::$PATH_SEPARATOR, $name);
            array_unshift($result['pages'], [
                'id'   => $id,
                'name' => array_pop($parts),
                'path' => $name,
                'childrenQuantity' => Pages::getChildrenQuantityByPath($name),
                'type'        => null,
                'isClickable' => true
            ]);
        }

        /** set extra items */
        if (($path = $request->path) && !$request->search) {
            $parts = explode(Pages::$PATH_SEPARATOR, $path);
            array_pop($parts);
            $backPath = implode(Pages::$PATH_SEPARATOR, $parts);
            $backItem = [
                'id'   => 'back',
                'name' => static::t('Back'),
                'path' => $backPath,
                'type' => null,
                'isClickable' => true
            ];

            if (
                count(explode(Pages::$PATH_SEPARATOR, $path)) < 2
                && $path !== (string) static::t('Brands')
            ) {
                $pathItem = [
                    'id'   => $currentPathId,
                    'name' => $path,
                    'path' => $path . Pages::$PATH_SEPARATOR,
                    'type' => 'path',
                    'isClickable' => false
                ];
            } else {
                $pathItem = [
                    'id'          => $currentPathId,
                    'name'        => $path,
                    'path'        => $path . Pages::$PATH_SEPARATOR,
                    'type'        => 'path',
                    'isClickable' => true
                ];
            }

            array_unshift($result['pages'], $pathItem);
            array_unshift($result['pages'], $backItem);
        }

        $this->printAjax($result);
        die();
    }

    /**
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }
}
