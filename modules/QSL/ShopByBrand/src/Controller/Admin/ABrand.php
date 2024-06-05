<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

class ABrand extends \XLite\Controller\Admin\ACL\Catalog
{
    protected $isBrandListEditable;

    /**
     * Check whether the brand list is editable, or not.
     *
     * @return bool
     */
    public function isBrandListEditable()
    {
        if (!isset($this->isBrandListEditable)) {
            $this->isBrandListEditable = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->getBrandAttributeId();
        }

        return $this->isBrandListEditable;
    }

    /**
     * Get link to the Brands module settings page.
     *
     * @return string
     */
    public function getModuleSettingsLink()
    {
        return $this->buildURL('module', '', ['moduleId' => $this->getBrandsModuleId()]);
    }

    /**
     * Get ID of the Brands module.
     *
     * @return int
     */
    protected function getBrandsModuleId()
    {
        return 'QSL-ShopByBrand';
    }
}
