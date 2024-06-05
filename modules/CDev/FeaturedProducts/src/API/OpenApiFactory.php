<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API;

use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\OpenApi;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OpenApiFactory extends \XLite\API\OpenApiFactory
{
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = parent::__invoke($context);

        // Front page featured products (filter out category_id param)
        $pathItem = $openApi->getPaths()->getPath('/api/front_page/featured/{product_id}');
        $operation = $pathItem->getDelete();

        $openApi->getPaths()->addPath('/api/front_page/featured/{product_id}', $pathItem->withDelete(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() === 'product_id')
            )
        ));

        // Category page featured products (filter out id param)
        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/featured');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/featured', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() === 'category_id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/featured/{product_id}');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/featured/{product_id}', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/featured/{product_id}');
        $operation = $pathItem->getDelete();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/featured/{product_id}', $pathItem->withDelete(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        return $openApi;
    }
}
