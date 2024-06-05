<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Data;

use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use XLite\Model\AEntity;

class MetadataDTO extends BaseConverter
{
    /**
     * Convert entity to DTO
     *
     * @param AEntity $entity
     *
     * @return array
     */
    public function convert(AEntity $entity): array
    {
        return [
            'id'           => $entity->getId(),
            'localId'      => $entity->getLocalId(),
            'entityType'   => $entity->getEntityType(),
            'externalData' => $entity->getExternalData()
        ];
    }
}
