<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Create indexes for tables after upgrade
 *
 * @Extender\Mixin
 */
abstract class Database extends \XLite\Core\Database implements \XLite\Base\IDecorator
{
    /**
     * loadClassMetadata event handler
     *
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs Event arguments
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        parent::loadClassMetadata($eventArgs);

        $classMetadata = $eventArgs->getClassMetadata();

        if (\XLite\Model\Order::class == $classMetadata->getName()) {

            if (!isset($classMetadata->table['indexes']['delayedPaymentSavedCardId'])) {
                $classMetadata->table['indexes']['delayedPaymentSavedCardId'] = array(
                    'columns' => array(
                        'delayedPaymentSavedCardId',
                    ),
                );
            }

            if (!isset($classMetadata->table['indexes']['xpaymentsBuyWithWallet'])) {
                $classMetadata->table['indexes']['xpaymentsBuyWithWallet'] = array(
                    'columns' => array(
                        'xpaymentsBuyWithWallet',
                    ),
                );
            }
        }
    }
}
