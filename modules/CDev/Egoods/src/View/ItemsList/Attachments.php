<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\ItemsList;

use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Input\Checkbox\OnOff;

/**
 * @Extender\Mixin
 */
class Attachments extends \CDev\FileAttachments\View\ItemsList\Attachments
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Egoods/product/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Egoods/product/script.js';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return parent::defineColumns() + [
                'private' => [
                    static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Free/Paid'),
                    static::COLUMN_CLASS   => 'CDev\Egoods\View\FormField\Inline\Input\Checkbox\Switcher\FreePaid',
                    static::COLUMN_ORDERBY => 250,
                ],
            ];
    }

    /**
     * Prepare field params for
     *
     * @param array                                                       $column
     * @param \CDev\FileAttachments\Model\Product\Attachment $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $params = $column[static::COLUMN_PARAMS];

        if (
            isset($column[static::COLUMN_CODE])
            && $column[static::COLUMN_CODE] == 'private'
            && !$this->isAttachmentAllowedToBePrivate($entity)
        ) {
            $params[OnOff::PARAM_DISABLED] = true;
            $params[OnOff::PARAM_DISABLED_TITLE] = static::t('File is available by public URL');
        }

        return $params;
    }

    /**
     * Check if attachment cannot be private
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $entity
     *
     * @return bool
     */
    protected function isAttachmentAllowedToBePrivate($entity)
    {
        return $entity->getPrivate() || $entity->canBePrivate();
    }
}
