<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.tips_container", zone="admin")
 */
class SimpleTipsBlock extends \XLite\View\AView
{
    public const PARAM_TIP_ID  = 'tipId';
    public const PARAM_CLASSES = 'tipClasses';

    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array_keys(static::getTips())
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TIP_ID  => new \XLite\Model\WidgetParam\TypeString('Tip id', '', ''),
            static::PARAM_CLASSES => new \XLite\Model\WidgetParam\TypeCollection('Tip classes', '', []),
        ];
    }

    protected static function getTipContent()
    {
        return '';
    }

    protected static function getTips()
    {
        return [
            'sitemap'               => 'sitemap1',
            'seo_page404_settings'  => 'seo_page404_settings1',
            'seo_homepage_settings' => 'seo_homepage_settings1',
            'settings'              => 'settings1',
        ];
    }

    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            ['tips/simple_block/styles.less']
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tips/simple_block/body.twig';
    }

    protected function isVisible()
    {
        return true;
    }

    public function getTipId()
    {
        return \XLite::getController()->getTarget();
    }

    public function getAdditionalClasses()
    {
        $classes = $this->getParam(static::PARAM_CLASSES);

        return is_array($classes)
            ? implode(' ', $classes)
            : '';
    }

    protected function isTipBlockVisible($currentTarget): bool
    {
        $prohibitedPages = $this->getProhibitedPages();
        $currentPage     = \XLite\Core\Request::getInstance()->page ?? '';

        return !isset($prohibitedPages[$currentTarget])
            || !in_array($currentPage, $prohibitedPages[$currentTarget], true);
    }

    protected function getProhibitedPages(): array
    {
        return [
            'settings' => [
                \XLite\Controller\Admin\Settings::COMPANY_PAGE,
                \XLite\Controller\Admin\Settings::ENVIRONMENT_PAGE
            ]
        ];
    }
}
