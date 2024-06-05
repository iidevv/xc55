<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Metadata\Resource\Factory;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

/**
 * Allow move openapi_context attribute from class-based ApiResource to resource's operations
 *
 * Annotation example: ApiPlatform\ApiResource(attributes={"openapi_context"={"tags"={"Product"}}})
 */
class TagsDecorator implements ResourceMetadataFactoryInterface
{
    /**
     * @var ResourceMetadataFactoryInterface
     */
    private $decorated;

    /**
     * @param ResourceMetadataFactoryInterface $decorated
     */
    public function __construct(ResourceMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass): ResourceMetadata
    {
        $result = $this->decorated->create($resourceClass);

        $openapi_context = $result->getAttribute('openapi_context');
        if (!$openapi_context) {
            return $result;
        }

        $tags = $openapi_context['tags'] ?? null;
        if (!$tags) {
            return $result;
        }

        return $result
            ->withCollectionOperations($this->processOperations($result->getCollectionOperations(), $tags))
            ->withItemOperations($this->processOperations($result->getItemOperations(), $tags));
    }

    /**
     * @param array $operations
     * @param array $tags
     *
     * @return array
     */
    private function processOperations(array $operations, array $tags): array
    {
        foreach ($operations as $k => $operation) {
            $operations[$k]['openapi_context']['tags'] = $tags;
        }

        return $operations;
    }
}
