<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (!$this->isNew()) {
            $list['attachments'] = static::t('Attachments');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list['attachments'] = 'modules/CDev/FileAttachments/product_tab.twig';
        }

        return $list;
    }

    // }}}

    /**
     * Remove file
     *
     * @return void
     */
    protected function doActionRemoveAttachment()
    {
        $attachment = \XLite\Core\Database::getRepo('CDev\FileAttachments\Model\Product\Attachment')
            ->find(Request::getInstance()->id);

        if ($attachment) {
            $attachment->getProduct()->getAttachments()->removeElement($attachment);
            \XLite\Core\Database::getEM()->remove($attachment);
            \XLite\Core\TopMessage::addInfo('Attachment has been deleted successfully');
            $this->setPureAction(true);
        } else {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Attachment is not deleted');
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Update files
     *
     * @return void
     */
    protected function doActionUpdateAttachments()
    {
        $changed = false;

        $repository = \XLite\Core\Database::getRepo('CDev\FileAttachments\Model\Product\Attachment');

        $toDelete = (array)Request::getInstance()->delete;
        $data = Request::getInstance()->data;

        if ($data && is_array($data)) {
            foreach ($data as $id => $row) {
                if (!in_array($id, array_keys($toDelete))) {
                    $attachment = $repository->find($id);

                    if ($attachment) {
                        $attachment->map(
                            $this->prepareAttachmentDataForMapping($row)
                        );
                        $changed = true;
                    }
                }
            }
        }

        if (!empty($toDelete)) {
            $repository->deleteInBatchById($toDelete);
            $changed = true;
        }

        if ($changed) {
            \XLite\Core\TopMessage::addInfo('Attachments have been updated successfully');
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function prepareAttachmentDataForMapping(array $data)
    {
        unset($data['_changed']);

        return $data;
    }
}
