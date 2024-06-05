<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['attachmentsPrivate'] = [];

        return $columns;
    }

    /**
     * Get column value for 'attachmentsPrivate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getAttachmentsPrivateColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getAttachments() as $attachment) {
            $result[] = $this->formatBoolean($attachment->getPrivate());
        }

        return $result;
    }

    /**
     * Format attachment model
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return string
     */
    protected function formatAttachmentModel(\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $isUrl = $attachment->getStorage() && $attachment->getStorage()->isURL();

        return $this->formatStorageModel(
            $attachment->getStorage(),
            $attachment->getPrivate() ?: ($isUrl ? false : null)
        );
    }
}
