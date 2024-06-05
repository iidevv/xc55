<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Admin extends \CDev\FileAttachments\View\Product\Admin
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['history'] = [
            'weight'     => 200,
            'title'      => static::t('History of downloads'),
            'url_params' => [
                'target'     => 'product',
                'page'       => 'attachments',
                'product_id' => $this->getProduct()->getProductId(),
                'subpage'    => 'history',
            ],
            'template'   => 'modules/CDev/Egoods/product/history.twig',
        ];

        return $list;
    }

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
     * Get item class
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return string
     */
    protected function getItemClass(\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $class = explode(' ', parent::getItemClass($attachment));

        if ($attachment->getPrivate()) {
            $class[] = 'private';
        }

        return implode(' ', $class);
    }
}
