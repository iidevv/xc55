<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Create;

use Qualiteam\SkinActYotpoReviews\Core\Command\ACreateUpdateCommand;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;
use XC\ProductVariants\Model\ProductVariant as ProductVariantModel;
use XLite\Core\Database;

class ProductVariant extends ACreateUpdateCommand implements ICommand
{
    /**
     * @throws \Exception
     */
    protected function executeCommand(): void
    {
        $variants = Database::getRepo(ProductVariantModel::class)?->findVariantsToCreateYotpoId();

        foreach ($variants as $variant) {
            $this->getResultYotpoRequest($variant);

            if ($this->isErrorResult()) {
                $this->logError();
            } else {
                $this->setYotpoId('variant');
                $this->setIsYotpoSync();
                $this->persistEntity();
                $this->updateEntity();
            }

            sleep(1);
        }
    }
}
