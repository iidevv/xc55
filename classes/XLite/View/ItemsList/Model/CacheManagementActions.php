<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\View\Button\Simple;
use XLite\View\Button\SimpleLink;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Class CacheManagementActions
 */
class CacheManagementActions extends \XLite\View\AView implements ProviderInterface
{
    /**
     * Returns CSS Files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'cache_management_actions/style.less';

        return $list;
    }

    /**
     * Returns JS Files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'rebuild/script.js';
        $list[] = 'cache_management_actions/script.js';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'cache_management_actions/body.twig';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected static function defineColumns()
    {
        return [
            'name' => [
                'name'     => static::t('Name'),
                'template' => 'cache_management_actions/cell/name.twig',
            ],
            'view' => [
                'name'     => static::t('Action'),
                'template' => 'cache_management_actions/cell/action.twig',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'name'        => static::t('Re-deploy the store'),
                'description' => static::t('Re-deploy the store help text'),
                'view'        => Simple::class,
                'viewParams'  => [
                    AButton::PARAM_LABEL => static::t('Start'),
                    AButton::PARAM_STYLE => 'always-enabled regular-main-button btn rebuild-btn',
                ],
            ],
            [
                'name'        => static::t('Calculate quick data'),
                'description' => static::t('Calculate quick data help text'),
                'view'        => SimpleLink::class,
                'viewParams'  => [
                    AButton::PARAM_LABEL => static::t('Start'),
                    AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    Link::PARAM_LOCATION => $this->buildURL('cache_management', 'quick_data'),
                ],
            ],
            [
                'name'        => static::t('Clear all caches'),
                'description' => static::t('Clear all caches text'),
                'view'        => SimpleLink::class,
                'viewParams'  => [
                    AButton::PARAM_LABEL => static::t('Start'),
                    AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    Link::PARAM_LOCATION => $this->buildURL('cache_management', 'clear_cache'),
                ],
            ],
            [
                'name'        => static::t('Recalculate ViewLists'),
                'description' => static::t('Recalculate ViewLists text'),
                'view'        => SimpleLink::class,
                'viewParams'  => [
                    AButton::PARAM_LABEL => static::t('Start'),
                    AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    Link::PARAM_LOCATION => $this->buildURL('cache_management', 'rebuild_view_lists'),
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBodyLines()
    {
        $lines   = $this->getData();
        $columns = $this->getColumns();

        $result = [];
        foreach ($lines as $lineRaw) {
            $line = [
                'entity'  => $lineRaw,
                'columns' => [],
            ];
            foreach ($columns as $columnRaw) {
                $column            = $columnRaw;
                $column['value']   = $lineRaw[$column['serviceName']];
                $line['columns'][] = $column;
            }
            $result[] = $line;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        $defaults = [
            'name'        => '',
            'serviceName' => '',
            'template'    => null,
            'class'       => null,
        ];
        $result   = [];

        foreach (static::defineColumns() as $serviceName => $columnRaw) {
            $column                = array_merge($defaults, $columnRaw);
            $column['serviceName'] = $serviceName;
            $result[$serviceName]  = $column;
        }

        return $result;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Hold on a moment, please. Redeploy is in progress' => static::t('Hold on a moment, please. Redeploy is in progress'),
            'Could not create a rebuild scenario'               => static::t('Could not create a rebuild scenario'),
            'Are you sure?'                                     => static::t('Are you sure?'),
        ];
    }
}
