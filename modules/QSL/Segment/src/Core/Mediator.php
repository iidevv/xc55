<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

use XLite\InjectLoggerTrait;

/**
 * Event mediator
 */
class Mediator extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * Valid flag
     *
     * @var boolean
     */
    protected $valid = false;

    /**
     * Stored messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Check - valid API or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Get anonymous ID
     *
     * @return string
     */
    public function getAnonymousId()
    {
        return \XLite\Core\Session::getInstance()->getID();
    }

    /**
     * Get profile fingerprint
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param \XLite\Model\Address $address Address OPTIONAL
     *
     * @return string
     */
    public function getProfileFingerprint(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null)
    {
        if (!$address) {
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
        }

        return md5(json_encode($this->assembleIdentifyMessage($profile, $address)));
    }

    // {{{ Events

    /**
     * 'login' event
     *
     * @param \XLite\Model\Profile $profile Profile
     */
    public function doLogin(\XLite\Model\Profile $profile)
    {
        if (!\XLite\Core\Session::getInstance()->is_alias_throwed) {
            $this->addMessage(
                'alias',
                $this->assembleAliasMessage($profile),
                true
            );
            \XLite\Core\Session::getInstance()->is_alias_throwed = true;
        }

        $this->addMessage(
            'identify',
            $this->assembleIdentifyMessage($profile),
            true
        );

        if ($this->isTrackAllowed('Logged_In')) {
            $this->addMessage(
                'track',
                $this->assembleLoginMessage($profile),
                true
            );
        }

        if ($profile->getMembership()) {
            $this->addMessage(
                'group',
                $this->assembleGroupMessage($profile),
                true
            );
        }
    }

    /**
     * 'login failed' event
     *
     * @param mixed $result Result code
     */
    public function doLoginFailed($result)
    {
        if ($this->isTrackAllowed('Log_In_Failed')) {
            $this->addMessage(
                'track',
                $this->assembleLoginFailedMessage($result)
            );
        }
    }

    /**
     * 'logoff' event
     */
    public function doLogoff()
    {
        //$this->addMessage('reset', array(), true);

        if ($this->isTrackAllowed('Logged_Off')) {
            $this->addMessage(
                'track',
                $this->assembleLogoffMessage(),
                true
            );
        }
    }

    /**
     * Update profile
     *
     * @param \XLite\Model\Profile $profile    Profile
     * @param \XLite\Model\Address $address    Address OPTIONAL
     * @param boolean              $ignoreAJAX Ignore AJAX flag OPTIONAL
     */
    public function doUpdateProfile(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null, $ignoreAJAX = false)
    {
        if (!$address) {
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
        }

        $this->addMessage(
            'identify',
            $this->assembleIdentifyMessage($profile, $address),
            $ignoreAJAX
        );

        if ($this->isTrackAllowed('Update_Profile')) {
            $this->addMessage(
                'track',
                $this->assembleUpdateProfileMessage($profile, $address),
                $ignoreAJAX
            );
        }

        if ($profile->getMembership()) {
            $this->addMessage(
                'group',
                $this->assembleGroupMessage($profile),
                $ignoreAJAX
            );
        }
    }

    /**
     * 'track(Viewed Product Category)' event
     *
     * @param \XLite\Model\Category $category Category
     */
    public function doViewCategory(\XLite\Model\Category $category)
    {
        if ($this->isTrackAllowed('Viewed_Product_Category')) {
            $this->addMessage(
                'track',
                $this->assembleViewCategoryMessage($category)
            );
        }
    }

    /**
     * 'track(Viewed Product)' event
     *
     * @param \XLite\Model\Product $product Product
     */
    public function doViewProduct(\XLite\Model\Product $product)
    {
        if ($this->isTrackAllowed('Viewed_Product')) {
            $this->addMessage(
                'track',
                $this->assembleViewProductMessage($product)
            );
        }
    }

    /**
     * 'track(Added Product)' event
     *
     * @param \XLite\Model\OrderItem $item Order item
     */
    public function doAddProductToCart(\XLite\Model\OrderItem $item)
    {
        if ($this->isTrackAllowed('Added_Product')) {
            $this->addMessage(
                'track',
                $this->assembleAddProductMessage($item)
            );
        }
    }

    /**
     * 'track(Removed Product)' event
     *
     * @param \XLite\Model\OrderItem $item Order item
     */
    public function doRemoveProductFromCart(\XLite\Model\OrderItem $item)
    {
        if ($this->isTrackAllowed('Removed_Product')) {
            $this->addMessage(
                'track',
                $this->assembleRemoveProductMessage($item)
            );
        }
    }

    /**
     * 'track(Completed Order)' event
     *
     * @param \XLite\Model\Order $order Order
     */
    public function doCompleteOrder(\XLite\Model\Order $order)
    {
        if ($this->isTrackAllowed('Completed_Order')) {
            $this->addMessage(
                'track',
                $this->assembleCompleteOrderMessage($order)
            );
        }
    }

    /**
     * 'track(Update product quantity)' event
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param integer                $oldAmount Old amount
     */
    public function doUpdateProductQuantity(\XLite\Model\OrderItem $item, $oldAmount)
    {
        if ($this->isTrackAllowed('Updated_Product_Quantity')) {
            $this->addMessage(
                'track',
                $this->assembleUpdateProductQuantityMessage($item, $oldAmount)
            );
        }
    }

    /**
     * 'track(Change shipping)' event
     *
     * @param integer $newShippingId New shipping ID
     * @param integer $oldShippingId Old shipping ID
     */
    public function doChangeShipping($newShippingId, $oldShippingId)
    {
        if ($this->isTrackAllowed('Change_Shipping')) {
            $this->addMessage(
                'track',
                $this->assembleChangeShippingMessage($newShippingId, $oldShippingId)
            );
        }
    }

    /**
     * 'track(Change payment)' event
     *
     * @param integer $newPaymentId New payment ID
     * @param integer $oldPaymentId Old payment ID
     */
    public function doChangePayment($newPaymentId, $oldPaymentId)
    {
        if ($this->isTrackAllowed('Change_Payment')) {
            $this->addMessage(
                'track',
                $this->assembleChangePaymentMessage($newPaymentId, $oldPaymentId)
            );
        }
    }

    /**
     * 'track(Search Products)' event
     *
     * @param array $data Data
     */
    public function doProductsSearch(array $data)
    {
        if ($this->isTrackAllowed('Search_Products')) {
            $this->addMessage(
                'track',
                $this->assembleProductsSearchMessage($data)
            );
        }
    }

    /**
     * 'track(Category Filter)' event
     *
     * @param \XLite\Model\Category $category Category
     * @param array                 $data     Data
     */
    public function doCategoryFilter(\XLite\Model\Category $category, array $data)
    {
        if ($this->isTrackAllowed('Change_Products_Filter')) {
            $this->addMessage(
                'track',
                $this->assembleCategoryFilterMessage($category, $data)
            );
        }
    }

    /**
     * 'track(Change Language)' event
     *
     * @param \XLite\Model\Language $from From
     * @param \XLite\Model\Language $to   To
     */
    public function doChangeLanguage(\XLite\Model\Language $from, \XLite\Model\Language $to)
    {
        if ($this->isTrackAllowed('Change_Language')) {
            $this->addMessage(
                'track',
                $this->assembleChangeLanguageMessage($from, $to)
            );
        }
    }

    // }}}

    // {{{ Assemblers

    /**
     * Assemble message for 'alias' request
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return array
     */
    protected function assembleAliasMessage(\XLite\Model\Profile $profile)
    {
        return [
            $profile->getProfileId(),
        ];
    }

    /**
     * Assemble message for 'identify' request
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param \XLite\Model\Address $address Address OPTIONAL
     *
     * @return array
     */
    protected function assembleIdentifyMessage(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null)
    {
        $message = [
            $profile->getProfileId(),
            [
                'createdAt' => date('c', $profile->getAdded()),
                'email'     => $profile->getLogin(),
                'id'        => $profile->getProfileId(),
                'username'  => $profile->getLogin(),
            ],
        ];

        if (
            \XLite\Core\Session::getInstance()->checkoutEmail
            && \XLite\Core\Session::getInstance()->checkoutEmail != $profile->getLogin()
        ) {
            $message[1]['checkoutEmail'] = \XLite\Core\Session::getInstance()->checkoutEmail;
        }

        if (!$address) {
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
        }
        if ($address) {
            $message[1]['address'] = [
                'city'       => $address->getCity(),
                'country'    => $address->getCountry() ? $address->getCountry()->getCode() : 'N/A',
                'postalCode' => $address->getZipcode(),
                'state'      => $address->getState() ? $address->getState()->getState() : 'N/A',
                'street'     => $address->getStreet(),
            ];
            $message[1]['firstName'] = $address->getFirstname();
            $message[1]['lastName']  = $address->getLastname();
            $message[1]['name']      = $address->getName();
            $message[1]['phone']     = $address->getPhone();
            $message[1]['title']     = $address->getTitle();

            // Mixpanel
            $message[1]['$country_code'] = $address->getCountry() ? $address->getCountry()->getCode() : 'N/A';
            $message[1]['$city']         = $address->getCity();
            $message[1]['$region']       = $address->getState() ? $address->getState()->getState() : 'N/A';
        }

        return $message;
    }

    /**
     * Assemble message for 'track (login)' request
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return array
     */
    protected function assembleLoginMessage(\XLite\Model\Profile $profile)
    {
        return [
            'event'      => 'Logged In',
            'properties' => [
                'userId' => $profile->getProfileId(),
                'email'  => $profile->getLogin(),
            ],
        ];
    }

    /**
     * Assemble message for 'track (login failed)' request
     *
     * @param string $result Result code
     *
     * @return array
     */
    protected function assembleLoginFailedMessage($result)
    {
        switch ($result) {
            case \XLite\Core\Auth::RESULT_INVALID_SECURE_HASH:
                $message = 'Trying to log in using an invalid secure hash string';
                break;

            case \XLite\COre\Auth::RESULT_PASSWORD_NOT_EQUAL:
                $message = 'Password is wrong';
                break;

            case \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED:
                $message = 'Account is locked';
                break;

            case \XLite\Core\Auth::RESULT_PROFILE_IS_ANONYMOUS:
                $message = 'Account is anonymous';
                break;

            default:
                $message = 'Unknown reason';
        }

        return [
            'event'      => 'Log In Failed',
            'properties' => [
                'code'    => $result,
                'message' => $message,
            ],
        ];
    }

    /**
     * Assemble message for 'track (update profile)' request
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param \XLite\Model\Address $address Address OPTIONAL
     *
     * @return array
     */
    protected function assembleUpdateProfileMessage(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null)
    {
        return [
            'event'      => 'Update Profile',
            'properties' => [
                'userId' => $profile->getProfileId(),
                'email'  => $profile->getLogin(),
            ],
        ];
    }

    /**
     * Assemble message for 'group' request
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return array
     */
    protected function assembleGroupMessage(\XLite\Model\Profile $profile)
    {
        return [
            'membership_' . $profile->getMembership()->getMembershipId(),
            [
                'id'   => $profile->getMembership()->getMembershipId(),
                'name' => $profile->getMembership()->getName(),
            ],
        ];
    }

    /**
     * Assemble message for 'track (logoff)' request
     *
     * @return array
     */
    protected function assembleLogoffMessage()
    {
        return [
            'event' => 'Logged Off',
        ];
    }

    /**
     * Assemble view category message
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return array
     */
    protected function assembleViewCategoryMessage(\XLite\Model\Category $category)
    {
        return [
            'event'      => 'Viewed Product Category',
            'properties' => [
                'category' => $category->getStringPath(),
            ],
        ];
    }

    /**
     * Assemble view product message
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    protected function assembleViewProductMessage(\XLite\Model\Product $product)
    {
        return [
            'event'      => 'Viewed Product',
            'properties' => [
                'id'       => $product->getProductId(),
                'sku'      => $product->getSku(),
                'name'     => $product->getName(),
                'price'    => $product->getDisplayPrice(),
                'category' => $product->getCategory()->getStringPath(),
            ],
        ];
    }

    /**
     * Assemble add product message
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return array
     */
    protected function assembleAddProductMessage(\XLite\Model\OrderItem $item)
    {
        $product = $item->getProduct();

        return [
            'event'      => 'Added Product',
            'properties' => [
                'id'       => $product->getProductId(),
                'sku'      => $item->getSku(),
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getAmount(),
                'category' => $product->getCategory()->getStringPath(),
            ],
        ];
    }

    /**
     * Assemble remove product message
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return array
     */
    protected function assembleRemoveProductMessage(\XLite\Model\OrderItem $item)
    {
        $product = $item->getProduct();

        return [
            'event'      => 'Removed Product',
            'properties' => [
                'id'       => $product->getProductId(),
                'sku'      => $item->getSku(),
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getAmount(),
                'category' => $product->getCategory()->getStringPath(),
            ],
        ];
    }

    /**
     * Assemble complete order message
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function assembleCompleteOrderMessage(\XLite\Model\Order $order)
    {
        $result = [
            'event'      => 'Completed Order',
            'properties' => [
                'orderId'  => $order->getOrderNumber(),
                'total'    => $order->getTotal(),
                'shipping' => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
                'tax'      => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX),
                'discount' => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT),
                'currency' => $order->getCurrency()->getCode(),
                'products' => [],
                'paymentStatus' => [
                    'code' => $order->getPaymentStatusCode(),
                    'name' => $order->getPaymentStatus()
                        ? $order->getPaymentStatus()->getName()
                        : '',
                ],
                'shippingStatus' => [
                    'code' => $order->getShippingStatusCode(),
                    'name' => $order->getShippingStatus()
                        ? $order->getShippingStatus()->getName()
                        : '',
                ],
            ],
        ];

        foreach ($order->getItems() as $item) {
            $result['properties']['products'][] = [
                'id'       => $item->getProduct()->getProductId(),
                'sku'      => $item->getSku(),
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getAmount(),
                'category' => $item->getProduct()->getCategory()->getStringPath(),
            ];
        }

        return $result;
    }

    /**
     * Assemble update product quantity message
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param integer                $oldAmount Old amount
     *
     * @return array
     */
    protected function assembleUpdateProductQuantityMessage(\XLite\Model\OrderItem $item, $oldAmount)
    {
        $product = $item->getProduct();

        return [
            'event'      => 'Updated Product Quantity',
            'properties' => [
                'id'                  => $product->getProductId(),
                'sku'                 => $item->getSku(),
                'name'                => $item->getName(),
                'price'               => $item->getPrice(),
                'quantity'            => intval($item->getAmount()),
                'category'            => $product->getCategory()->getStringPath(),
                'quantity_difference' => $item->getAmount() - $oldAmount,
            ],
        ];
    }

    /**
     * Assemble change shipping message
     *
     * @param integer $newShippingId New shipping ID
     * @param integer $oldShippingId Old shipping ID
     *
     * @return boolean
     */
    public function assembleChangeShippingMessage($newShippingId, $oldShippingId)
    {
        /** @var \XLite\Model\Shipping\Method $newShipping */
        $newShipping = $newShippingId
            ? \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($newShippingId)
            : null;

        /** @var \XLite\Model\Shipping\Method $oldShipping */
        $oldShipping = $oldShippingId
            ? \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($oldShippingId)
            : null;

        /** @var \XLite\Model\Currency $currency */
        $currency = \XLite::getController()->getCart()->getCurrency();

        $newShippingCost = null;
        $oldShippingCost = null;
        $modifier = \XLite::getController()->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        /** @var \XLite\Model\Shipping\Rate[] $rates */
        $rates = $modifier ? $modifier->getRates() : [];
        foreach ($rates as $rate) {
            if ($rate->getMethod()->getMethodId() == $newShippingId) {
                $newShippingCost = $currency->roundValue($rate->getTotalRate());
            }
            if ($rate->getMethod()->getMethodId() == $oldShippingId) {
                $oldShippingCost = $currency->roundValue($rate->getTotalRate());
            }
        }

        return [
            'event'      => 'Change Shipping',
            'properties' => [
                'old_shipping_id'   => $oldShippingId,
                'new_shipping_id'   => $newShippingId,
                'old_shipping'      => $oldShipping ? $oldShipping->getName() : '',
                'new_shipping'      => $newShipping ? $newShipping->getName() : '',
                'old_shipping_cost' => $oldShippingCost,
                'new_shipping_cost' => $newShippingCost,
                'currency'          => $currency->getCode(),
            ],
        ];
    }

    /**
     * Assemble change payment message
     *
     * @param integer $newPaymentId New payment ID
     * @param integer $oldPaymentId Old payment ID
     *
     * @return boolean
     */
    public function assembleChangePaymentMessage($newPaymentId, $oldPaymentId)
    {
        /** @var \XLite\Model\Payment\Method $newPayment */
        $newPayment = $newPaymentId
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($newPaymentId)
            : null;

        /** @var \XLite\Model\Payment\Method $newPayment */
        $oldPayment = $oldPaymentId
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($oldPaymentId)
            : null;

        return [
            'event'      => 'Change Payment',
            'properties' => [
                'old_payment_id' => $oldPaymentId,
                'new_payment_id' => $newPaymentId,
                'old_payment'    => $oldPayment ? $oldPayment->getName() : '',
                'new_payment'    => $newPayment ? $newPayment->getName() : '',
            ],
        ];
    }

    /**
     * Assemble search products message
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function assembleProductsSearchMessage(array $data)
    {
        return [
            'event'      => 'Search Products',
            'properties' => $data,
        ];
    }

    /**
     * Assemble category filter message
     *
     * @param \XLite\Model\Category $category Category
     * @param array                 $data     Data
     *
     * @return array
     */
    protected function assembleCategoryFilterMessage(\XLite\Model\Category $category, array $data)
    {
        return [
            'event'      => 'Change Products Filter',
            'properties' => [
                'category' => $category->getStringPath(),
                'filter'   => $data['filter'],
            ],
        ];
    }

    /**
     * Assemble change language message
     *
     * @param \XLite\Model\Language $from From
     * @param \XLite\Model\Language $to   To
     *
     * @return array
     */
    protected function assembleChangeLanguageMessage(\XLite\Model\Language $from, \XLite\Model\Language $to)
    {
        return [
            'event'      => 'Change Language',
            'properties' => [
                'from' => $from->getCode(),
                'to'   => $to->getCode(),
            ],
        ];
    }

    // }}}

    // {{{ Service

    /**
     * Get stored messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = \XLite\Core\Session::getInstance()->segment_messages;
        unset(\XLite\Core\Session::getInstance()->segment_messages);

        return is_array($messages) ? $messages : [];
    }

    /**
     * Display messages as AJAX event
     */
    public function displayAJAXMessages()
    {
        $result = [];
        $messages = \XLite\Core\Session::getInstance()->segment_messages;
        if ($messages) {
            foreach ($messages as $i => $message) {
                if (!$message['ignoreAJAX']) {
                    $result[] = $message;
                    unset($messages[$i]);
                }
            }
            \XLite\Core\Session::getInstance()->segment_messages = $messages;
        }

        if ($result) {
            header('event-segment.push: ' . json_encode(['list' => $result]));
        }
    }

    /**
     * Get options block
     *
     * @return array
     */
    public function getOptionsBlock()
    {
        $code = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        return [
            'anonymousId' => $this->getAnonymousId(),
            'context'     => [
                'plugin' => [
                    'name'    => 'X-Cart',
                    'version' => '', // todo: module vеrsion
                ],
                'locale' => $code . '_' . strtoupper($code),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function __construct()
    {
        parent::__construct();

        $this->valid = PHP_SAPI != 'cli'
            && !defined('LC_CACHE_BUILDING')
            && !empty(\XLite\Core\Config::getInstance()->QSL->Segment->write_key)
            && !\XLite::isAdminZone();
    }

    /**
     * Send API request
     *
     * @param string $type     Message type
     * @param array $arguments Message argument
     *
     * @return boolean
     */
    protected function send($type, array $arguments)
    {
        return (bool)\QSL\Segment\Core\API::getInstance()->call($type, $arguments);
    }

    /**
     * Add message
     *
     * @param string  $type       Message type
     * @param array   $arguments  Message argument
     * @param boolean $ignoreAJAX Ignore AJAX flag
     *
     * @return boolean
     */
    protected function addMessage($type, array $arguments = [], $ignoreAJAX = false)
    {
        $result = false;
        if ($this->isValid()) {
            $arguments = $this->preprocessBrowserMessage($type, $arguments);

            $messages = \XLite\Core\Session::getInstance()->segment_messages;
            if (!is_array($messages)) {
                $messages = [];
            }
            $messages[] = ['type' => $type, 'arguments' => $arguments, 'ignoreAJAX' => $ignoreAJAX];
            \XLite\Core\Session::getInstance()->segment_messages = $messages;

            $this->getLogger('QSL-Segment')->debug('Add message to query', [
                'type'      => $type,
                'arguments' => $arguments
            ]);

            $result = true;
        }

        return $result;
    }

    /**
     * Preprocess message form browser
     *
     * @param string $type     Message type
     * @param array $arguments Message argument
     *
     * @return array
     */
    protected function preprocessBrowserMessage($type, array $arguments)
    {
        $result = $arguments;
        switch ($type) {
            case 'track':
                if (isset($arguments['event'])) {
                    $result = [
                        $arguments['event'],
                        $arguments['properties'] ?? new \stdClass(),
                    ];
                    if (isset($arguments[0])) {
                        $result[] = $arguments[0];
                    } elseif (isset($arguments['options'])) {
                        $result[] = $arguments['options'];
                    }
                }
                break;

            default:
        }

        return $result;
    }

    /**
     * Check - track is allowed or not
     *
     * @param string $name Short name
     *
     * @return boolean
     */
    protected function isTrackAllowed($name)
    {
        $name = 'event_' . $name;

        return (bool)\XLite\Core\Config::getInstance()->QSL->Segment->$name;
    }

    // }}}
}
