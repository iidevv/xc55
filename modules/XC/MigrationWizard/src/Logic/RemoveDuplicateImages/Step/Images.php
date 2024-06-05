<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\RemoveDuplicateImages\Step;

/**
 * Step
 */
class Images extends \XC\MigrationWizard\Logic\RemoveDuplicateImages\Step\AStep
{
    // {{{ Data <editor-fold desc="Data" defaultstate="collapsed">

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Image\Product\Image');
    }

    // }}} </editor-fold>

    // {{{ Row processing <editor-fold desc="Row processing" defaultstate="collapsed">

    /**
     * Process model
     *
     * @param \XLite\Model\Base\Image $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\Base\Image $model)
    {
        $productImagesRepo = $this->getRepository();

        $duplicateImages = $productImagesRepo->findBy([
            'product' => $model->getProduct(),
        ]);
        $hash = \Includes\Utils\FileManager::getHash($model->getStoragePath(), false, false);

        foreach ($duplicateImages as $duplicate) {
            if ($duplicate->getId() === $model->getId()) {
                continue;
            } elseif (
                $hash
                && $hash === \Includes\Utils\FileManager::getHash($duplicate->getStoragePath(), false, false)
            ) {
                $productImagesRepo->delete($model, false);
            }
        }

        $productVariantImagesRepo = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\Image\ProductVariant\Image');

        if ($productVariantImagesRepo) {
            $duplicateImages = $productVariantImagesRepo->countByProductAndHash($model->product, $model->hash);

            if ($duplicateImages > 0) {
                $productImagesRepo->delete($model, false);
            }
        }
    }

    // }}} </editor-fold>
}
