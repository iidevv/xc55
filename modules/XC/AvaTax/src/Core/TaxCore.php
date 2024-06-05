<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Core;

use XC\AvaTax\Main;
use XLite\InjectLoggerTrait;

/**
 * AcaTax client
 */
class TaxCore extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    // for https://developer.avalara.com/api-reference/avatax/rest/v2/methods/Transactions/VoidTransaction/
    public const DOC_DELETED = 'DocDeleted';
    public const DOC_VOIDED  = 'DocVoided';

    // for https://developer.avalara.com/api-reference/avatax/rest/v2/methods/Transactions/AdjustTransaction/
    public const PRICE_ADJUSTED = 'PriceAdjusted';

    public const FAILED_BACKEND_STATUSES = [\XLite\Model\Payment\BackendTransaction::STATUS_FAILED, \XLite\Model\Payment\BackendTransaction::STATUS_INITIALIZED];

    public const OTHER          = 'Other';

    protected const COLORADO_FEE_TEXT       = 'Colorado Fee';
    protected const COLORADO_FEE_TAX_CODE   = 'OF400000';
    protected const COLORADO_FEE_TAX_NAME   = 'coloradoFee';
    protected const COLORADO_FEE_STATE_CODE = 'CO';

    /**
     * Valid status
     *
     * @var boolean
     */
    protected $valid;

    protected string $baseURL;

    public function __construct()
    {
        parent::__construct();
        $this->baseURL = \XLite\Core\Config::getInstance()->XC->AvaTax->developermode
            ? 'https://sandbox-rest.avatax.com/api/v2/'
            : 'https://rest.avatax.com/api/v2/';
    }

    /**
     * Check valid status
     *
     * @return boolean
     */
    public function isValid()
    {
        if (!isset($this->valid)) {
            $config = \XLite\Core\Config::getInstance()->XC->AvaTax;
            $this->valid = $config->account_number && $config->license_key;
        }

        return $this->valid;
    }

    public function adjustTransactionRequest(\XC\AvaTax\Model\Order $order, string $reason, string $reasonDescription = '')
    {
        $messages     = [];
        $oldOrderData = $this->getInformation($order, $messages);
        $dataProvider = new DataProvider\Order($order);
        $data         = $dataProvider->getAdjustTransactionModel($oldOrderData, $reason, $reasonDescription);
        $this->avataxRequest(
            "companies/{$dataProvider->companyCodeEncoded()}/transactions/{$dataProvider->transactionCodeEncoded()}/adjust",
            $data
        );
    }

    public function refundTransactionRequest(\XLite\Model\Payment\BackendTransaction $transaction, $order = null)
    {
        $dataProvider = new DataProvider\Order($order ?: $transaction->getPaymentTransaction()->getOrder());
        $data         = $dataProvider->getRefundTransactionModel($transaction);
        $this->avataxRequest(
            "companies/{$dataProvider->companyCodeEncoded()}/transactions/{$dataProvider->transactionCodeEncoded()}/refund",
            $data
        );
    }

    public function voidTransactionRequest(\XC\AvaTax\Model\Order $order, string $reason = self::DOC_VOIDED)
    {
        $dataProvider = new DataProvider\Order($order);
        $data         = $dataProvider->getVoidTransactionModel($reason);
        $this->avataxRequest(
            "companies/{$dataProvider->companyCodeEncoded()}/transactions/{$dataProvider->transactionCodeEncoded()}/void",
            $data
        );

        // void possible refund transactions
        foreach (($order->getPaymentTransactions() ?? []) as $paymentTransaction) {
            foreach (($paymentTransaction->getBackendTransactions() ?? []) as $backendTransaction) {
                if (
                    stripos($backendTransaction->getType(), 'refund') !== false
                    && !in_array($backendTransaction->getStatus(), self::FAILED_BACKEND_STATUSES)
                ) {
                    $refundTransactionCode = $dataProvider->transactionCodeEncoded(DataProvider\Order::REFUND_POSTFIX . $backendTransaction->getId());
                    $this->avataxRequest(
                        "companies/{$dataProvider->companyCodeEncoded()}/transactions/$refundTransactionCode/void",
                        $data
                    );
                }
            }
        }
    }

    // {{{ Test connection

    /**
     * Test connection
     *
     * @return boolean
     */
    public function testConnection(array &$messages = [])
    {
        $data = $this->getTestInformation();
        [$result, $taxes] = $this->avataxRequest('transactions/create', $data);

        $result = $taxes && empty($taxes['error']);
        if (!$result && !empty($taxes['error']['message'])) {
            $messages[] = $taxes['error']['message'];
        }

        return $result;
    }

    /**
     * Get test information
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function getTestInformation()
    {
        $config = \XLite\Core\Config::getInstance()->XC->AvaTax;

        $post = [
            'client'       => 'X-Cart 5',
            'companyCode'  => $config->companycode,
            'customerCode' => 'test',
            'date'         => date('Y-m-d'),
            'type'         => 'SalesOrder',
            'commit'       => false,
            'lines'        => [
                [
                    'number'    => 1,
                    'quantity'  => 1,
                    'amount'    => 10,
                ],
            ],
            'addresses'    => [
                'ShipFrom' => [
                    'line1'       => '300 Meline Drive',
                    'city'        => 'Edmond',
                    'region'      => 'OK',
                    'country'     => 'US',
                    'postalCode'  => '73034',
                ],
                'ShipTo' => [
                    'line1'       => '350 Fifth Avenue',
                    'city'        => 'New York',
                    'region'      => 'NY',
                    'country'     => 'US',
                    'postalCode'  => '10118',
                ],
            ],
        ];

        return $post;
    }

    // }}}

    // {{{ Address validation

    /**
     * Validate address
     *
     * @param mixed $address Address
     *
     * @return array
     */
    public function validateAddress($address)
    {
        $config = \XLite\Core\Config::getInstance()->XC->AvaTax;

        $data = $this->assembleAddressValidationRequest($address);
        $url = $this->getAddressValidateURL($data);
        $request = new \XLite\Core\HTTP\Request($url);
        $request->verb = 'GET';
        $request->setHeader(
            'Authorization',
            'Basic ' . base64_encode(
                $config->account_number . ':' . $config->license_key
            )
        );
        $result = $request->sendRequest();

        $result = $result && $result->body
            ? json_decode($result->body, true)
            : null;

        $this->getLogger('XC-AvaTax')->debug('API request', [
            'url'      => $url,
            'request'  => $data,
            'response' => $result
        ]);

        return $result
            ? $this->processAddressValidation($result, $address)
            : [null, null];
    }

    /**
     * Assemble address validation request
     *
     * @param mixed $address Address

     * @return array
     */
    protected function assembleAddressValidationRequest($address)
    {
        $result = [];

        if (is_array($address) && is_object(current($address))) {
            // Address from XLite\View\Model\Address\Address
            foreach ($address as $field) {
                $parts = explode('_', $field->getName(), 2);
                switch ($parts[1]) {
                    case 'street':
                        $result['line1'] = $field->getValue();
                        break;

                    case 'city':
                        $result['city'] = $field->getValue();
                        break;

                    case 'state_id':
                        if ($field->getValue()) {
                            $state = \XLite\Core\Database::getRepo('XLite\Model\State')->find($field->getValue());
                            if ($state) {
                                $result['region'] = $state->getCode();
                            }
                        }
                        break;

                    case 'custom_state':
                        if (empty($result['region']) && $field->getValue()) {
                            $result['region'] = $field->getValue();
                        }
                        break;

                    case 'country_code':
                        $result['country'] = $field->getValue();
                        break;

                    case 'zipcode':
                        $result['postalCode'] = $field->getValue();
                        break;

                    default:
                }
            }
        } elseif (is_array($address) && !empty($address['line1'])) {
            // Address from static::getStateTax()
            $result = $address;
        } elseif (is_array($address) && !empty($address['location_country'])) {
            // Address from XLite\View\Model\Settings
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
                    ->find($address['location_country']);
            $hasStates = $country && $country->hasStates();
            $state = ($address['location_state'] && $hasStates)
                ? \XLite\Core\Database::getRepo('XLite\Model\State')->findById($address['location_state'])
                : null;
            $result = [
                'line1'       => $address['location_address'],
                'city'        => $address['location_city'],
                'region'      => $state ? $state->getCode() : '',
                'country'     => $address['location_country'],
                'postalCode'  => $address['location_zipcode'],
            ];
        } elseif (is_array($address) && !empty($address['state_id'])) {
            // Address from XLite\Controller\Customer\Checkout
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
                    ->find($address['country_code']);
            $hasStates = $country && $country->hasStates();
            $state = ($address['state_id'] && $hasStates)
                ? \XLite\Core\Database::getRepo('XLite\Model\State')->findById($address['state_id'])
                : null;
            $result = [
                'line1'       => $address['street'],
                'city'        => $address['city'],
                'region'      => $state ? $state->getCode() : '',
                'country'     => $address['country_code'],
                'postalCode'  => $address['zipcode'],
            ];
        }

        return $result;
    }

    /**
     * Process address validation
     *
     * @param array $data    Raw data
     * @param mixed $address Address
     *
     * @return array
     */
    protected function processAddressValidation(array $data, $address)
    {
        $result = [];

        if (!empty($data['messages'])) {
            foreach ($data['messages'] as $message) {
                if (empty($message['details'])) {
                    // System error
                    $this->getLogger('XC-AvaTax')->error($message['summary']);

                    $result[] = [
                        'name'    => $message['refersTo'],
                        'message' => $message['details'],
                    ];
                } else {
                    // Business logic error
                    $cell = $this->assembleAddressValidationMessage($message, $address);
                    if ($cell) {
                        $result[] = $cell;
                    }
                }
            }
        } elseif (!empty($data['validatedAddresses'])) {
            $address = $this->assembleAddressSanitaizedData($address, current($data['validatedAddresses']));
        }

        return [$address, $result];
    }

    /**
     * Assemble address validation message
     *
     * @param array $message Raw message
     * @param mixed $address Address
     *
     * @return array
     */
    protected function assembleAddressValidationMessage(array $message, $address)
    {
        $result = null;

        if (is_array($address) && is_object(current($address))) {
            // Address from XLite\View\Model\Address\Address
            switch ($message['refersTo']) {
                case 'Address':
                case 'Address.Line0':
                    $names = ['street'];
                    break;

                case 'Address.City':
                    $names = ['city'];
                    break;

                case 'Address.Region':
                    $names = ['state_id', 'custom_state'];
                    break;

                case 'Address.Country':
                    $names = ['country_code'];
                    break;

                case 'Address.PostalCode':
                    $names = ['zipcode'];
                    break;

                default:
                    $names = [];
            }

            $field = null;
            foreach ($address as $f) {
                $parts = explode('_', $f->getName(), 2);
                foreach ($names as $name) {
                    if ($name == $parts[1] && $f->getValue()) {
                        $field = $f;
                        break 2;
                    }
                }
            }

            // Assemble message
            if ($field) {
                $result = [
                    'name'    => $field->getName(),
                    'field'   => $field,
                    'message' => $message['details'],
                ];
            }
        } elseif (is_array($address) && !empty($address['line1'])) {
            // Address from static::getStateTax()
            $parts = explode('.', $message['refersTo']);
            if ($parts[0] == 'address') {
                $result = [
                    'name'    => $parts[1],
                    'message' => $message['details'],
                ];
            }
        } elseif (is_array($address) && !empty($address['location_country'])) {
            // Address from XLite\View\Model\Settings
            switch ($message['refersTo']) {
                case 'Address':
                case 'Address.Line0':
                    $name = 'location_address';
                    break;

                case 'Address.City':
                    $name = 'location_city';
                    break;

                case 'Address.Region':
                    $name = 'location_state';
                    break;

                case 'Address.Country':
                    $name = 'location_country';
                    break;

                case 'Address.PostalCode':
                    $name = 'location_zipcode';
                    break;

                default:
                    $name = null;
            }

            if ($name) {
                $result = [
                    'name'    => $name,
                    'message' => $message['details'],
                ];
            }
        } elseif (is_array($address) && !empty($address['state_id'])) {
            // Address from XLite\Controller\Customer\Checkout
            switch ($message['refersTo']) {
                case 'Address':
                case 'Address.Line0':
                    $name = 'street';
                    break;

                case 'Address.City':
                    $name = 'city';
                    break;

                case 'Address.Region':
                    $name = 'state_id';
                    break;

                case 'Address.Country':
                    $name = 'country_code';
                    break;

                case 'Address.PostalCode':
                    $name = 'zipcode';
                    break;

                default:
                    $name = null;
            }

            if ($name) {
                $result = [
                    'name'    => $name,
                    'message' => $message['details'],
                ];
            }
        }

        return $result;
    }

    /**
     * Assemble address sanitized data
     *
     * @param mixed $address Address
     * @param array $data    Sanitized address
     *
     * @return array
     */
    protected function assembleAddressSanitaizedData($address, array $data)
    {
        if (is_array($address) && is_object(current($address))) {
            // Address from XLite\View\Model\Address\Address
            $country = null;
            $state = null;
            $oldStateValue = null;
            foreach ($data as $n => $value) {
                switch ($n) {
                    case 'line1':
                        $name = 'street';
                        break;

                    case 'region':
                        $name = 'state_id';
                        break;

                    case 'city':
                        $name = 'city';
                        break;

                    case 'country':
                        $name = 'country_code';
                        break;

                    case 'postalCode':
                        $name = 'zipcode';
                        break;

                    default:
                        $name = null;
                }

                if ($name) {
                    foreach ($address as $f) {
                        $parts = explode('_', $f->getName(), 2);
                        if ($name == $parts[1]) {
                            if ($name == 'country_code') {
                                $country = $f;
                            } elseif ($name == 'state_id') {
                                $state = $f;
                                $oldStateValue = $f->getValue();
                            }

                            $f->setValue($value);
                            break;
                        }
                    }
                }
            }

            if ($country && $state) {
                $sid = $this->processAddressState($country->getValue(), $state->getValue());
                if ($sid) {
                    $state->setValue($sid);
                } else {
                    $state->setValue($oldStateValue);
                }
            }
        } elseif (is_array($address) && !empty($address['line1'])) {
            // Address from static::getStateTax()
            foreach ($data as $n => $value) {
                if (isset($address[$n])) {
                    $address[$n] = $value;
                }
            }
        } elseif (is_array($address) && !empty($address['location_country'])) {
            // Address from XLite\View\Model\Settings
            foreach ($data as $n => $value) {
                switch ($n) {
                    case 'line1':
                        $name = 'location_address';
                        break;

                    case 'city':
                        $name = 'location_city';
                        break;

                    case 'region':
                        $name = 'location_state';
                        break;

                    case 'country':
                        $name = 'location_country';
                        break;

                    case 'postalCode':
                        $name = 'location_zipcode';
                        break;

                    default:
                        $name = null;
                }

                if ($name) {
                    $address[$name] = $value;
                }
            }

            $sid = $this->processAddressState($address['location_country'], $address['location_state']);
            if ($sid) {
                $address['location_state'] = $sid;
            } else {
                unset($address['location_state']);
            }
        } elseif (is_array($address) && !empty($address['state_id'])) {
            // Address from XLite\Controller\Customer\Checkout
            foreach ($data as $n => $value) {
                switch ($n) {
                    case 'line1':
                        $name = 'street';
                        break;

                    case 'city':
                        $name = 'city';
                        break;

                    case 'region':
                        $name = 'state_id';
                        break;

                    case 'country':
                        $name = 'country_code';
                        break;

                    case 'postalCode':
                        $name = 'zipcode';
                        break;

                    default:
                        $name = null;
                }

                if ($name) {
                    $address[$name] = $value;
                }
            }

            $sid = $this->processAddressState($address['country_code'], $address['state_id']);
            if ($sid) {
                $address['state_id'] = $sid;
            } else {
                unset($address['state_id']);
            }
        }

        return $address;
    }

    /**
     * Process address state
     *
     * @param string $countryCode Country code
     * @param string $stateCode   Country code
     *
     * @return integer
     */
    protected function processAddressState($countryCode, $stateCode)
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode($countryCode, $stateCode);

        return $state ? $state->getStateId() : null;
    }

    /**
     * Get getTax URL
     *
     * @return string
     */
    protected function getAddressValidateURL(array $data)
    {
        return $this->baseURL . 'addresses/resolve?' . http_build_query($data, null, '&');
    }

    // }}}

    // {{{ Tax calculation

    /**
     * Final calculation flag
     *
     * @var boolean
     */
    protected $finalCalculationFlag = false;

    /**
     * Get state tax
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    public function getStateTax(\XLite\Model\Order $order)
    {
        $result = [false, []];

        $messages = [];
        $data = $this->getInformation($order, $messages);
        if ($data) {
            return $this->createTransactionRequest($data) ?: $result;
        } else {
            $result[0] = $messages;
        }

        return $result;
    }

    /**
     * Set final calculation flag
     *
     * @param boolean $flag Flag
     *
     * @return void
     */
    public function setFinalCalculationFlag($flag)
    {
        $this->finalCalculationFlag = $flag;
    }

    /**
     * Process taxes
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function processTaxes(array $data)
    {
        $errors = false;
        $result = [];

        if (!empty($data['error'])) {
            $errors = [];
            $errorData = $data['error'];
            $this->getLogger('XC-AvaTax')->error($errorData['message']);
            $errors[] = $errorData['message'];
        } elseif (!empty($data['summary'])) {
            foreach ($data['summary'] as $row) {
                if ($row['tax'] > 0) {
                    $code = str_replace(' ', '_', $row['taxName']);
                    $name = $row['taxName'];

                    if (
                        isset($row['jurisType'])
                        && strtolower($row['jurisType']) === 'special'
                    ) {
                        $addName = $row['jurisName'] ?? 'Special';

                        $code = sprintf('%s_%s', $code, str_replace(' ', '_', $addName));
                        $name = sprintf('%s (%s)', $name, $addName);
                    }

                    $result[$code] = [
                        'code' => $code,
                        'name' => $name,
                        'cost' => $row['tax'],
                    ];
                }
            }
        }

        return [$errors, $result];
    }

    protected function getConfigCompany(\XLite\Model\Order $order)
    {
        return \XLite\Core\Config::getInstance()->Company;
    }

    /**
     * Get information
     *
     * @param \XLite\Model\Order $order     Order
     * @param array              &$messages Error messages
     *
     * @return array
     */
    protected function getInformation(\XLite\Model\Order $order, array &$messages)
    {
        $config = \XLite\Core\Config::getInstance()->XC->AvaTax;
        $address = $order->getProfile()->getBillingAddress();

        $company = $this->getConfigCompany($order);
        $currency = $order->getCurrency();

        $dataProvider = new DataProvider\Order($order);
        $post = [
            'client'       => 'X-Cart 5',
            'companyCode'  => $config->companycode,
            'code'         => $dataProvider->getTransactionCode() ?: '',
            'customerCode' => $order->getProfile()->getProfileId(),
            'currencyCode' => $currency->getCode(),
            'discount'     => 0,
            'date'         => date('Y-m-d'),
            'type'         => $this->shouldRecordTransaction() ? 'SalesInvoice' : 'SalesOrder',
            'commit'       => $this->isCommitOnPlaceOrder(),
            'lines'        => [],
            'addresses'    => [
                'ShipFrom' => [
                    'line1'       => $company->origin_address,
                    'city'        => $company->origin_city,
                    'region'      => $company->originState
                        ? $company->originState->getCode()
                        : '',
                    'country'     => $company->origin_country,
                    'postalCode'  => $company->origin_zipcode,
                ],
                'ShipTo' => [
                    'line1'       => $address->getStreet(),
                    'city'        => $address->getCity(),
                    'region'      => $address->getState()
                        ? $address->getState()->getCode()
                        : '',
                    'country'     => $address->getCountry()
                        ? $address->getCountry()->getCode()
                        : '',
                    'postalCode'  => $address->getZipcode(),
                ],
            ],
        ];

        $saddress = null;
        if ($order->isShippable() && !$order->getProfile()->isSameAddress() && $order->getProfile()->getShippingAddress()) {
            $saddress = $order->getProfile()->getShippingAddress();
            $post['addresses']['ShipTo'] = [
                'line1'       => $saddress->getStreet(),
                'city'        => $saddress->getCity(),
                'region'      => $saddress->getState()
                    ? $saddress->getState()->getCode()
                    : '',
                'country'     => $saddress->getCountry()
                    ? $saddress->getCountry()->getCode()
                    : '',
                'postalCode'  => $saddress->getZipcode(),
            ];
        }

        // Discount
        $cost = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT);
        if ($cost < 0) {
            $post['discount'] = $currency->roundValue(abs($cost));
        }

        foreach ($order->getItems() as $item) {
            $post['lines'][] = [
                'number'      => $item->getItemId(),
                'address'     => $saddress ? $saddress->getAddressId() : $address->getAddressId(),
                'itemCode'    => $this->assembleItemCode($item),
                'description' => $item->getName(),
                'quantity'    => $item->getAmount(),
                'amount'      => $currency->roundValue($item->getTotal()),
            ];

            if ($item->getProduct()->getAvaTaxCode()) {
                $post['lines'][count($post['lines']) - 1]['taxCode'] = $item->getProduct()->getAvaTaxCode();
            }

            if ($post['discount'] > 0) {
                $post['lines'][count($post['lines']) - 1]['discounted'] = true;
            }
        }

        // Shipping
        $shipping = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        if ($shipping && $shipping->getSelectedRate() && $shipping->getSelectedRate()->getMethod()) {
            $cost = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
            $post['lines'][] = [
                'number'   => \XLite\Model\Base\Surcharge::TYPE_SHIPPING,
                'quantity' => 1,
                'amount'   => $currency->roundValue($cost),
                'taxCode'  => 'FR',
            ];
        }

        // Any surcharges and handlings
        $cost = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_HANDLING);
        if ($cost > 0) {
            $post['lines'][] = [
                'number'   => \XLite\Model\Base\Surcharge::TYPE_HANDLING,
                'address'  => $saddress ? $saddress->getAddressId() : $address->getAddressId(),
                'quantity' => 1,
                'amount'   => $currency->roundValue($cost),
            ];
        }

        // Special fee tax
        if (self::shouldAddColoradoTax($order)) {
            $post = self::addColoradoTaxLine($post);
        }

        // Exemption number
        if ($order->getProfile()->getAvaTaxExemptionNumber()) {
            $post['exemptionNo'] = $order->getProfile()->getAvaTaxExemptionNumber();
        }

        // Usage type
        if ($order->getProfile()->getAvaTaxCustomerUsageType()) {
            $post['customerUsageType'] = $order->getProfile()->getAvaTaxCustomerUsageType();
        }

        return $post;
    }

    /**
     * Check - address verification is allowed or not
     *
     * @param mixed $address Address
     *
     * @return boolean
     */
    public function isAllowedAddressVerification($address)
    {
        $result = false;
        if (\XLite\Core\Config::getInstance()->XC->AvaTax->addressverif) {
            $assembledAddress = $this->assembleAddressValidationRequest($address);

            $result = in_array($assembledAddress['country'], ['US', 'CA']);
        }

        return $result;
    }

    /**
     * Assemble item code
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return string
     */
    protected function assembleItemCode(\XLite\Model\OrderItem $item)
    {
        return substr($item->getSku(), 0, 50);
    }

    /**
     * Check - is it last tax calculation or not
     *
     * @return boolean
     */
    public function isLastTaxCalculation()
    {
        return $this->finalCalculationFlag;
    }

    /**
     * Returns true if "Set Commit as true on place order" option is enabled
     *
     * @return bool
     */
    public function isCommitOnPlaceOrder()
    {
        return \XLite\Core\Config::getInstance()->XC->AvaTax->commit && $this->shouldRecordTransaction();
    }

    /**
     * Returns true if "Set Commit as true on place order" option is enabled
     *
     * @return bool
     */
    public function shouldRecordTransaction()
    {
        return \XLite\Core\Config::getInstance()->XC->AvaTax->record_transactions && $this->isLastTaxCalculation();
    }

    /**
     * Returns true if "Order is shippable and shipping address has state Colorado"
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return bool
     */
    protected static function shouldAddColoradoTax($order)
    {
        if (Main::isColoradoRetailDeliveryFeeCollectionEnabled() && $order->isShippable()) {
            if (!$order->getProfile()->isSameAddress() && $order->getProfile()->getShippingAddress()) {
                $address = $order->getProfile()->getShippingAddress();
            } else {
                $address = $order->getProfile()->getBillingAddress();
            }
            $state = $address->getState();

            return $state && $state->getCode() === self::COLORADO_FEE_STATE_CODE;
        }

        return false;
    }

    /**
     * @param array $post
     *
     * @return array
     */
    protected static function addColoradoTaxLine($post)
    {
        $post['lines'][] = [
            'number'      => self::COLORADO_FEE_TAX_NAME,
            'taxCode'     => self::COLORADO_FEE_TAX_CODE,
            'description' => self::COLORADO_FEE_TEXT,
            'quantity'    => 1,
            'amount'      => 0,
        ];

        return $post;
    }

    // }}}

    protected function createTransactionRequest(array $data): array
    {
        $result = [false, []];
        if ($data) {
            [$result, $taxes] = $this->avataxRequest('transactions/create', $data);
            $result = $taxes ? $this->processTaxes($taxes) : [false, []];
        }

        return $result;
    }

    /**
     * Low level POST request
     */
    protected function avataxRequest(string $url, array $data): array
    {
        $config  = \XLite\Core\Config::getInstance()->XC->AvaTax;
        $fullURL = $this->baseURL . $url;
        if (!(\XLite\Core\Config::getInstance()->XC->AvaTax->debugmode || \XLite\Core\Config::getInstance()->XC->AvaTax->developermode)) {
            $fullURL .= '?$include=SummaryOnly';
        }
        $request        = new \XLite\Core\HTTP\Request($fullURL);
        $request->body  = json_encode($data);
        $request->verb  = 'POST';
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader(
            'Authorization',
            'Basic ' . base64_encode(
                $config->account_number . ':' . $config->license_key
            )
        );
        $result = $request->sendRequest();

        $decodedResponse = $result && $result->body
            ? json_decode($result->body, true)
            : null;

        $this->getLogger('XC-AvaTax')->debug('API request', [
            'url'      => $fullURL,
            'request'  => $data,
            'response' => $decodedResponse,
        ]);

        return [$result, $decodedResponse];
    }
}
