<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model\Repo;

/**
 * Magic360 image
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'magic360';
    }

    /**
     * Define storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $result   = [];
        $result[] = '\Qualiteam\SkinActMagicImages\Model\Image';

        return $result;
    }
}
