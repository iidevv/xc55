<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Product\Customer\Search
{
    use \CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = $this->getOpenGraphMetaTags(false);

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphTitle()
    {
        return \XLite::getController()->getPageTitle() . ' : ' . self::getParam('substring');
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphType()
    {
        return 'product.group';
    }

    /**
     * Returns open graph url
     *
     * @return string
     */
    protected function getOpenGraphURL()
    {
        return \XLite\Core\URLManager::getCurrentURL();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getListHead()) . ' - ' . static::t('default-meta-description');
    }

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return false;
    }

    /**
     * Return OgMeta
     *
     * @return string
     */
    public function getOgMeta()
    {
        return false;
    }
}
