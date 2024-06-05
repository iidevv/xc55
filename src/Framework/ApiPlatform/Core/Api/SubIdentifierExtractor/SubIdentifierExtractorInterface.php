<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Api\SubIdentifierExtractor;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;

interface SubIdentifierExtractorInterface extends IdentifiersExtractorInterface
{
    public const SUB_IDENTIFIER_EXTRACTOR_TAG = 'xcart.api.sub_identifier_extractor';

    public function supportResourceClass(string $resourceClass): bool;

    public function supportItem(object $item): bool;
}
