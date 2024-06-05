<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Button;

/**
 * Remove button
 */
class FeedSettings extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names.
     */
    public const PARAM_ENTITY = 'entity';

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/ProductFeeds/button/feed_settings.css';

        return $list;
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ENTITY => new \XLite\Model\WidgetParam\TypeObject(
                'Entity',
                null,
                false,
                '\QSL\ProductFeeds\Model\ProductFeed'
            ),
        ];
    }

    /**
     * Return the default widget template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductFeeds/button/feed_settings.twig';
    }

    /**
     * Return the button style.
     *
     * @return string
     */
    protected function getStyle()
    {
        return 'remove'
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '');
    }

    /**
     * Return URL to the page with feed settings.
     *
     * @return string
     */
    protected function getSettingsPageUrl()
    {
        return \XLite\Core\Converter::buildURL(
            'product_feed',
            '',
            [
                'feed_id' => $this->getEntity()->getId(),
            ]
        );
    }

    /**
     * Return the entity parameter.
     *
     * @return \QSL\ProductFeeds\Model\ProductFeed
     */
    protected function getEntity()
    {
        return $this->getParam(static::PARAM_ENTITY);
    }
}
