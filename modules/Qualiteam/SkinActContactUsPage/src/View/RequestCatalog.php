<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\View;

use XLite\Core\Config;

class RequestCatalog extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActContactUsPage/contact_us/request_catalog.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getRequestCatalogUrl();
    }

    public function getRequestCatalogUrl() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->request_catalog_url;
    }

    public function getRequestCatalogImageUrl() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->request_catalog_image;
    }
}