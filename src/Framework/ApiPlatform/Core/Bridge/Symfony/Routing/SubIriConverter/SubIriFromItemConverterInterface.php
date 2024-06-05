<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter;

interface SubIriFromItemConverterInterface
{
    public const SUB_IRI_FROM_ITEM_CONVERTER_TAG = 'xcart.api.sub_iri_from_item_converter';

    public function supportIriFromItem(object $item, int $referenceType): bool;

    public function getIriFromItem(object $item, int $referenceType): string;
}
