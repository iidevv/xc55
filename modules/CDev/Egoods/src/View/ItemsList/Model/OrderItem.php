<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem
{
    /**
     * Postprocess inserted entity
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     * @param array                  $line   Array of entity data from request
     *
     * @return boolean
     */
    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        $result = parent::postprocessInsertedEntity($entity, $line);

        if ($result && \XLite\Controller\Admin\Order::isNeedProcessStock()) {
            // Create private attachments
            $entity->createPrivateAttachments();

            // Renew private attachments
            foreach ($entity->getPrivateAttachments() as $attachment) {
                $attachment->renew();
            }
        }

        return $result;
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);

        if ($result) {
            foreach ($entity->getPrivateAttachments() as $attachment) {
                $attachment->getRepository()->delete($attachment, false);
            }
        }

        return $result;
    }
}
