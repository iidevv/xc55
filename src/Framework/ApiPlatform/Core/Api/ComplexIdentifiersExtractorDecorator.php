<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Api;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use XCart\Framework\ApiPlatform\Core\Api\SubIdentifierExtractor\SubIdentifierExtractorInterface;

/**
 * The class allows you to be embedded in the mechanism of extracting its identifiers from the model
 */
final class ComplexIdentifiersExtractorDecorator implements IdentifiersExtractorInterface
{
    private IdentifiersExtractorInterface $inner;

    /**
     * @var SubIdentifierExtractorInterface[]
     */
    private array $subIdentifierExtractors = [];

    public function __construct(
        IdentifiersExtractorInterface $inner,
        iterable $subIdentifierExtractors
    ) {
        $this->inner = $inner;
        foreach ($subIdentifierExtractors as $subIdentifierExtractor) {
            $this->addSubIdentifierExtractors($subIdentifierExtractor);
        }
    }

    public function addSubIdentifierExtractors(SubIdentifierExtractorInterface $subIdentifierExtractor): void
    {
        $this->subIdentifierExtractors[] = $subIdentifierExtractor;
    }

    public function getIdentifiersFromResourceClass(string $resourceClass): array
    {
        foreach ($this->subIdentifierExtractors as $subIdentifierExtractor) {
            if ($subIdentifierExtractor->supportResourceClass($resourceClass)) {
                return $subIdentifierExtractor->getIdentifiersFromResourceClass($resourceClass);
            }
        }

        return $this->inner->getIdentifiersFromResourceClass($resourceClass);
    }

    public function getIdentifiersFromItem($item): array
    {
        foreach ($this->subIdentifierExtractors as $subIdentifierExtractor) {
            if ($subIdentifierExtractor->supportItem($item)) {
                return $subIdentifierExtractor->getIdentifiersFromItem($item);
            }
        }

        return $this->inner->getIdentifiersFromItem($item);
    }
}
