<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\ProductTags\Mapper;

class Tag
{
    /**
     * @param \XC\ProductTags\Model\Tag $tag
     *
     * @return array
     */
    public function mapToArray(\XC\ProductTags\Model\Tag $tag)
    {
        return [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'position' => $tag->getPosition()
        ];
    }
}