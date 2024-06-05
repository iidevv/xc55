<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;

/**
 * The class allows you to be embedded in the IRI model calculation mechanism for models that have a composite path with multiple identifiers
 */
final class ComplexIriConverterDecorator implements IriConverterInterface
{
    private IriConverterInterface $inner;

    /**
     * @var SubIriFromItemConverterInterface[]
     */
    private array $subIriFromItemConverters = [];

    public function __construct(
        IriConverterInterface $inner,
        iterable $subIriFromItemConverters
    ) {
        $this->inner = $inner;
        foreach ($subIriFromItemConverters as $subIriFromItemConverter) {
            $this->addSubIriFromItemConverter($subIriFromItemConverter);
        }
    }

    public function addSubIriFromItemConverter(SubIriFromItemConverterInterface $subIriFromItemConverter): void
    {
        $this->subIriFromItemConverters[] = $subIriFromItemConverter;
    }

    /**
     * @return object
     */
    public function getItemFromIri(string $iri, array $context = [])
    {
        return $this->inner->getItemFromIri($iri, $context);
    }

    public function getIriFromItem($item, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        foreach ($this->subIriFromItemConverters as $subIriFromItemConverter) {
            if ($subIriFromItemConverter->supportIriFromItem($item, $referenceType)) {
                return $subIriFromItemConverter->getIriFromItem($item, $referenceType);
            }
        }

        return $this->inner->getIriFromItem($item, $referenceType);
    }

    public function getIriFromResourceClass(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->inner->getIriFromResourceClass($resourceClass, $referenceType);
    }

    public function getItemIriFromResourceClass(string $resourceClass, array $identifiers, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->inner->getItemIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }

    public function getSubresourceIriFromResourceClass(string $resourceClass, array $identifiers, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->inner->getSubresourceIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }
}
