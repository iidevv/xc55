<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $tags = [];
        foreach ($object->getTags() as $tag) {
            $tags[] = $tag->getId();
        }

        $this->default->tags = $tags;
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $repo = \XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag');
        $object->replaceTagsByTags($repo->getListByIdOrName($this->default->tags));
    }
}
