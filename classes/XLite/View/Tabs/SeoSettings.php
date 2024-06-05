<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to seo settings
 */
class SeoSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'seo_page404_settings';
        $list[] = 'seo_homepage_settings';
        $list[] = 'settings';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'settings'              => [
                'weight'     => 100,
                'title'      => static::t('Website'),
                'template'   => 'settings/clean_url/body.twig',
                'url_params' => ['page' => 'CleanURL'],
            ],
            'seo_homepage_settings' => [
                'weight'     => 200,
                'title'      => static::t('Homepage'),
                'template'   => 'form_field/clean_urls/front_page_body.twig',
                'url_params' => ['page' => 'SeoHomepage'],
            ],
            'seo_page404_settings'  => [
                'weight'     => 300,
                'title'      => static::t('404 page'),
                'template'   => 'settings/clean_url/body.twig',
                'url_params' => ['page' => 'CleanURL'],
            ],
        ];
    }
}
