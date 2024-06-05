<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Model\ProductVariant;

use Doctrine\ORM\PersistentCollection;
use Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate\ACreateUpdate;
use Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate\IUpdate;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Model\AEntity;

class Update extends ACreateUpdate implements IUpdate
{
    public function __construct(
        private ProductVariant $productVariant
    ) {
        parent::__construct();
    }

    protected function isExcludedChange(array|PersistentCollection $change): bool
    {
        return $change[0] === 0.0 && $change[1] === 0;
    }

    protected function getExcludedKeysOnChange(): array
    {
        return [
            'defaultValue',
        ];
    }

    protected function getModelObjectForFindChanges(): AEntity
    {
        return $this->getProductVariant();
    }

    protected function getProductVariant(): ProductVariant
    {
        return $this->productVariant;
    }

    public function do(): void
    {
        $this->getEntityManager()?->addAfterFlushCallback(function() {
            $this->getProductVariant()->setIsYotpoSync(false);
        });
    }
}