<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View;

use XCart\Extender\Mapping\Extender;
use CDev\GoSocial\Logic\OgMeta;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class PageModify extends \CDev\SimpleCMS\View\Model\Page
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->schemaDefault['useCustomOG'] = [
            static::SCHEMA_CLASS      => 'CDev\GoSocial\View\FormField\Select\CustomOpenGraph',
            static::SCHEMA_LABEL      => 'Open Graph meta tags',
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_TRUSTED    => true,
            static::SCHEMA_FIELD_ONLY => false,
        ];

        $this->schemaDefault['showSocialButtons'] = [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Show social buttons',
            self::SCHEMA_REQUIRED => false,
        ];

        parent::__construct($params, $sections);
    }

    protected function setModelProperties(array $data)
    {
        $data['useCustomOG'] = $this->getPostedData('useCustomOG');
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $data['ogMeta'] = isset($nonFilteredData['postedData']['ogMeta'])
            ? OgMeta::prepareOgMeta($nonFilteredData['postedData']['ogMeta'])
            : '';

        parent::setModelProperties($data);
    }
}
