<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Strategy\UpdateProduct;

use Qualiteam\SkinActYotpoReviews\Core\Dispatcher\CreateProductDispatcher;
use Qualiteam\SkinActYotpoReviews\Core\Dispatcher\UpdateProductDispatcher;
use Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate\ACreateUpdate;
use Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate\IUpdate;
use XLite\Model\AEntity;
use XLite\Model\Product;

class Prod extends ACreateUpdate implements IUpdate
{
    public function __construct(
        private Product $product
    ) {
        parent::__construct();
    }

    public function do(): void
    {
        $this->getEntityManager()?->addAfterFlushCallback(function () {
            if (!$this->isCloneProduct()) {
                $dispatcher = $this->getDispatcher();

                $message = $dispatcher->getMessage();
                $this->getBus()?->dispatch($message);
            }
        });
    }

    protected function getDispatcher(): UpdateProductDispatcher|CreateProductDispatcher
    {
        return !$this->getProduct()->getYotpoId()
            ? new CreateProductDispatcher($this->getProduct())
            : new UpdateProductDispatcher($this->getProduct());
    }

    protected function isCloneProduct(): bool
    {
        return strpos($this->getProduct()->getName(), '[ clone ]');
    }

    protected function getProduct(): Product
    {
        return $this->product;
    }

    protected function getExcludedKeysOnChange(): array
    {
        return [
            'add_to_google_feed',
            'average_rating',
            'votes_count',
        ];
    }

    protected function getModelObjectForFindChanges(): AEntity
    {
        return $this->getProduct();
    }
}