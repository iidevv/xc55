<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    protected OpenApiFactoryInterface $decorated;

    protected OpenApiTagsRepositoryInterface $tagsRepository;

    public function __construct(OpenApiFactoryInterface $decorated, OpenApiTagsRepositoryInterface $tagsRepository)
    {
        $this->decorated = $decorated;
        $this->tagsRepository = $tagsRepository;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        // x-tagGroups
        $tagGroups = [];
        foreach ($this->tagsRepository->getTags() as $group => $tags) {
            $tagGroup = [
                'name' => $group,
                'tags' => [],
            ];
            foreach ($tags as $tag) {
                $tagGroup['tags'][] = $tag;
            }
            $tagGroups[] = $tagGroup;
        }
        $openApi = $openApi->withExtensionProperty('tagGroups', $tagGroups);

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/products');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/products', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() === 'category_id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/products/{product_id}/images/{image_id}');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/products/{product_id}/images/{image_id}', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/products/{product_id}/images/{image_id}');
        $operation = $pathItem->getDelete();
        $openApi->getPaths()->addPath('/api/products/{product_id}/images/{image_id}', $pathItem->withDelete(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/products/{product_id}/images/{image_id}');
        $operation = $pathItem->getPut();
        $openApi->getPaths()->addPath('/api/products/{product_id}/images/{image_id}', $pathItem->withPut(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/icon');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/icon', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/icon');
        $operation = $pathItem->getDelete();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/icon', $pathItem->withDelete(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/banner');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/banner', $pathItem->withGet(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        $pathItem = $openApi->getPaths()->getPath('/api/categories/{category_id}/banner');
        $operation = $pathItem->getDelete();
        $openApi->getPaths()->addPath('/api/categories/{category_id}/banner', $pathItem->withDelete(
            $operation->withParameters(
                array_filter($operation->getParameters(), static fn(Parameter $p) => $p->getName() !== 'id')
            )
        ));

        return $openApi;
    }
}
