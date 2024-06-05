<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryBanner\SubIriConverter;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\Image\Category\Banner as Image;

class SubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof Image;
    }

    /**
     * @param Image $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_category banners_get_item',
            [
                'category_id' => $item->getCategory()->getCategoryId(),
            ],
            $referenceType
        );
    }
}
