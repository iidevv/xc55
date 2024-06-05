<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model\Repo\Base;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Storage extends \XLite\Model\Repo\Base\Storage
{
    /**
     * Define all storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $list = parent::defineStorageRepositories();

        $list[] = 'CDev\FileAttachments\Model\Product\Attachment\Storage';

        return $list;
    }
}
