<?php


namespace Qualiteam\SkinActChangesToTrackingNumbers\Core\Mail\Order;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Mail\Order\TrackingCustomer;
use XLite\Model\Order;
use XLite\Model\Shipping\Method;


/**
 * @Extender\Mixin
 */
abstract class AOrder extends \XLite\Core\Mail\Order\AOrder
{
    public function __construct(Order $order)
    {
        parent::__construct($order);

        $shipping = Database::getRepo(Method::class)->find($order->getShippingId());

        $this->appendData([
            'shippingMethod'                    => $shipping,
            'includeShippingMethodInstructions' => ($this instanceof TrackingCustomer),
        ]);
    }
}
