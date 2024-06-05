<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;

use XC\ThemeTweaker\Core\Notifications\Data;
use XLite\View\AView;

class Product extends AView implements DataSource
{
    private $data;

    public static function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    public function __construct(Data $data)
    {
        $this->data = $data;
        parent::__construct([]);
    }

    public static function buildNew(Data $data)
    {
        return new static($data);
    }

    /**
     * @return array
     */
    protected static function getTemplateDirectories()
    {
        return [
            'low_limit_warning'
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/product/body.twig';
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->data->getData()['product'] ?? null;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProduct()
            ? $this->getProduct()->getSku()
            : '';
    }
}
