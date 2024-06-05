<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

use XLite\Core\Exception\ClosedStorefrontException;

/**
 * Abstract controller for Customer interface
 */
abstract class ACustomer extends \XLite\Controller\AController
{
    /**
     * cart
     *
     * @var \XLite\Model\Cart
     */
    protected $cart;

    /**
     * Initial cart fingerprint
     *
     * @var array
     */
    protected $initialCartFingerprint;

    /**
     * Breadcrumbs
     *
     * @var \XLite\View\Location\Node[]
     */
    protected $locationPath;

    /**
     * Runtime cache
     * @var array
     */
    protected $addressFields;

    // {{{ Breadcrumbs

    /**
     * Return current location path
     *
     * @return \XLite\View\Location
     */
    public function getLocationPath()
    {
        if ($this->locationPath === null) {
            $this->defineLocationPath();
        }

        return $this->locationPath;
    }

    /**
     * Return true if checkout layout is used
     *
     * @return boolean
     */
    public function isCheckoutLayout()
    {
        return in_array($this->getTarget(), ['checkout', 'checkoutPayment'], true);
    }

    /**
     * Stub for XC\ThemeTweaker module.
     *
     * @return bool
     */
    public function isInInlineEditorMode()
    {
        return false;
    }

    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function isAccountLinksVisible()
    {
        return !$this->isLogged();
    }

    /**
     * Method to create the location line
     *
     * @return void
     */
    protected function defineLocationPath()
    {
        $this->locationPath = [];

        // Ability to add part to the line
        $this->addBaseLocation();

        // Ability to define last element in path via short function
        $location = $this->getLocation();

        if ($location) {
            $this->addLocationNode($location);
        }
    }

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $responsiveClass = \XLite\Core\Request::isMobileDevice()
            ? 'responsive-mobile'
            : 'responsive-desktop';

        if (\XLite\Core\Request::isTablet()) {
            $responsiveClass = 'responsive-tablet';
        }

        $classes[] = $responsiveClass;

        return $classes;
    }

    /**
     * Checks if desktop navbar should be rendered (used to defer duplicate navbar loading)
     * TODO: Enable this when it will be properly working
     *
     * @return bool
     */
    public function shouldRenderDesktopNavbar()
    {
        return true;
    }

    /**
     * Checks if mobile navbar should be rendered (used to defer duplicate navbar loading)
     * TODO: Enable this when it will be properly working
     *
     * @return bool
     */
    public function shouldRenderMobileNavbar()
    {
        return true;
    }

    /**
     *
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return null;
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        // Common element for all location lines
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }

    /**
     * Add node to the location line
     *
     * @param string $name     Node title
     * @param string $link     Node link OPTIONAL
     * @param array  $subnodes Node subnodes OPTIONAL
     *
     * @return void
     */
    protected function addLocationNode($name, $link = null, array $subnodes = null)
    {
        $this->locationPath[] = \XLite\View\Location\Node::create($name, $link, $subnodes);
    }

    // }}}

    /**
     * Return current category Id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return parent::getCategoryId() ?: $this->getRootCategoryId();
    }

    /**
     * Return cart instance
     *
     * @param null|boolean $doCalculate Flag: completely recalculate cart if true OPTIONAL
     *
     * @return \XLite\Model\Order
     */
    public function getCart($doCalculate = null)
    {
        return \XLite\Model\Cart::getInstance($doCalculate ?? $this->markCartCalculate());
    }

    /**
     * Defines the canonical URL for the page
     *
     * @return string
     */
    public function getCanonicalURL()
    {
        $params = $this->getAllParams();
        $target = $params['target'] ?? '';
        unset($params['target']);
        // Product pages do not count the category identificator for the canonical URL
        if ($target === 'product') {
            unset($params['category_id']);
        }

        $method = \XLite\Core\Config::getInstance()->Security->customer_security
            ? 'getSecureShopURL'
            : 'getShopURL';

        if ($target === 'main') {
            $canonicalURL = $this->buildFullURL();
        } elseif (LC_USE_CLEAN_URLS && $target === 'product') {
            $canonicalURL = $this->$method(
                \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildURLProductCanonical($params)
            );
        } else {
            $canonicalURL = $this->$method(
                \XLite\Core\Converter::buildURL($target, '', $params, null, true)
            );
        }

        return $canonicalURL;
    }

    /**
     * Check if page has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        $router = \XLite\Core\Router::getInstance();

        return LC_USE_CLEAN_URLS && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && count($router->getActiveLanguagesCodes()) > 1;
    }

    /**
     * Return page alternative language urls
     *
     * @return bool
     */
    public function getAlternateLangUrls()
    {
        $request = \XLite\Core\Request::getInstance();
        $result  = [];

        [$target, $params] = \XLite\Core\Converter::parseCleanUrl($request->url, $request->last, $request->rest, $request->ext);

        $url = \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildURL($target, $params);
        $url = strtok($url, '?');

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
            $langUrl = $language->getCode() . '/' . $url;

            $result[\XLite\Core\Converter::langToLocale($language->getCode())] = \Includes\Utils\URLManager::getShopURL($langUrl);
        }

        $result['x-default'] = \Includes\Utils\URLManager::getShopURL($url);

        return $result;
    }

    /**
     * Controller marks the cart calculation.
     * In some cases we do not need to recalculate the cart.
     * We need it mainly on the checkout page.
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return false;
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        $result = [];

        if (!$this->markCartCalculate()) {
            $result[] = 'shippingMethodsHash';
            $result[] = 'shippingTotal';
            $result[] = 'shippingMethodId';
        }

        return $result;
    }

    /**
     * getURL
     *
     * @param array $params URL parameters OPTIONAL
     *
     * @return string
     */
    public function getURL(array $params = [])
    {
        $params = array_merge($this->getAllParams(), $params);
        $target = $params['target'] ?? '';

        if ($target === \XLite::TARGET_DEFAULT) {
            $target = '';
            unset($params['target']);
        } else {
            unset($params['target']);
        }

        return $this->buildURL($target, '', $params);
    }

    /**
     * Get the full URL of the page
     * Example: getShopURL('cart.php') = "http://domain/dir/cart.php
     *
     * @param string  $url    Relative URL OPTIONAL
     * @param boolean $secure Flag to use HTTPS OPTIONAL
     * @param array   $params Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getShopURL($url = '', $secure = null, array $params = [])
    {
        if ($secure === null && $this->isFullCustomerSecurity()) {
            $secure = true;
        }

        return parent::getShopURL($url, $secure, $params);
    }

    /**
     * Get current profile username
     *
     * @return string
     */
    public function getProfileUsername()
    {
        return $this->getCart()->getProfile()
            ? $this->getCart()->getProfile()->getLogin()
            : '';
    }

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        if (!$this->checkStorefrontAccessibility()) {
            $this->closeStorefront();
        }

        if (!$this->isServiceController()) {
            // Save initial cart fingerprint
            $this->initialCartFingerprint = $this->getCart()->getEventFingerprint($this->getCartFingerprintExclude());
        }

        parent::handleRequest();
    }

    /**
     * Send headers
     *
     * @param array $additional Additional headers OPTIONAL
     *
     * @return void
     */
    public static function sendHeaders($additional = [])
    {
        parent::sendHeaders($additional);
        $contentSecurityPolicy = \Includes\Utils\ConfigParser::getOptions(['other', 'content_security_policy']);
        if ($contentSecurityPolicy !== null && $contentSecurityPolicy !== 'disabled') {
            \XLite::getInstance()->addHeader('Content-Security-Policy', $contentSecurityPolicy);
        }
    }

    /**
     * Check - is top 'Continue Shopping' button is visible or not
     *
     * @return boolean
     */
    public function isContinueShoppingVisible()
    {
        return false;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess() && $this->checkFormId();
    }

    /**
     * Return true if request contains 'profile_id' but this parameter does not match to currently logged in user
     *
     * @return boolean
     */
    protected function checkProfile()
    {
        $result = true;

        if (\XLite\Core\Request::getInstance()->profile_id) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->find(\XLite\Core\Request::getInstance()->profile_id);
            $result  = $profile && \XLite\Core\Auth::getInstance()->checkProfile($profile);
        }

        return $result;
    }

    /**
     * Stub for the CMS connectors
     *
     * @return boolean
     */
    protected function checkStorefrontAccessibility()
    {
        return \XLite\Core\Auth::getInstance()->isAccessibleStorefront();
    }

    /**
     * Perform some actions to prohibit access to storefront
     *
     * @return void
     */
    protected function closeStorefront()
    {
        throw new ClosedStorefrontException();
    }

    /**
     * Recalculates the shopping cart
     *
     * @param boolean $silent
     *
     * @throws \Exception
     */
    protected function updateCart($silent = false)
    {
        $cart    = $this->getCart();
        $updated = $cart->updateEmptyShippingID();

        \XLite\Core\Database::getEM()->transactional(function (\Doctrine\ORM\EntityManagerInterface $em) use (&$cart, $updated) {
            if ($updated || $this->markCartCalculate()) {
                if ($cart->isManaged()) {
                    $em->lock($cart, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
                }
                $cart->updateOrder();
            }
        });

        if (!$silent) {
            $this->assembleEvent();
        }
    }

    /**
     * Assemble updateCart event
     *
     * @return boolean
     */
    protected function assembleEvent()
    {
        $currentFingerprint = $this->getCart()->getEventFingerprint($this->getCartFingerprintExclude());
        $diff               = $this->getCartFingerprintDifference(
            $this->initialCartFingerprint,
            $currentFingerprint
        );

        if ($diff) {
            $actualDiff = $this->posprocessCartFingerprintDifference($diff);
            if ($actualDiff) {
                \XLite\Core\Event::updateCart($actualDiff);
            }
        }

        return (bool) $diff;
    }

    /**
     * Get fingerprint difference
     *
     * @param array $old Old fingerprint
     * @param array $new New fingerprint
     *
     * @return array
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $diff = [];

        $items = [];

        // Assembly changed
        foreach ($new['items'] as $n => $cell) {
            $found = false;
            foreach ($old['items'] as $i => $oldCell) {
                if ($cell['key'] == $oldCell['key']) {
                    if ($cell['quantity'] != $oldCell['quantity']) {
                        $cell['quantity_change'] = $cell['quantity'] - $oldCell['quantity'];

                        $items[] = $cell;
                    }

                    unset($old['items'][$i]);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cell['quantity_change'] = $cell['quantity'];
                $items[]                 = $cell;
            }
        }

        // Assemble removed
        foreach ($old['items'] as $cell) {
            $cell['quantity_change'] = $cell['quantity'] * -1;
            $cell['quantity']        = 0;

            unset($cell['is_limit']);
            $items[] = $cell;
        }

        if ($items) {
            $diff['items'] = $items;
        }

        $cellKeys = [
            'shippingTotal',
            'shippingMethodId',
            'paymentMethodId',
            'shippingAddressId',
            'billingAddressId',
            'shippingAddressFields',
            'billingAddressFields',
            'sameAddress',
            'shippingMethodsHash',
            'paymentMethodsHash',
            'itemsCount',
        ];

        foreach ($cellKeys as $name) {
            $old[$name] = $old[$name] ?? '';
            $new[$name] = $new[$name] ?? '';

            if ($old[$name] != $new[$name]) {
                $diff[$name] = $new[$name];
            }
        }

        // Assemble total diff
        if ($old['total'] != $new['total']) {
            $diff['total'] = $new['total'] - $old['total'];
        }

        return $diff;
    }

    /**
     * Postprocess cart fingerprint differences and exclude some of them
     *
     * @param array $diff Differences
     *
     * @return array
     */
    protected function posprocessCartFingerprintDifference(array $diff)
    {
        $result = [];

        foreach ($diff as $name => $data) {
            $isAvail = true;

            $method = 'postprocessDifference' . \Includes\Utils\Converter::convertToUpperCamelCase($name);
            if (method_exists($this, $method)) {
                // postprocessDifference + <param name>
                $isAvail = $this->{$method}($data);
            }

            if ($isAvail) {
                $result[$name] = $data;
            }
        }

        return $result;
    }

    /**
     * Email can not be empty, so if it's empty, it means there wasn't an email field in the form. In that case, remove
     * email from the differences list.
     * The function always returns true.
     *
     * @param array $data Shipping address fields that were changed
     *
     * @return bool
     */
    protected function postprocessDifferenceShippingAddressFields(array &$data): bool
    {
        if (empty($data['email'])) {
            unset($data['email']);
        }
        return true;
    }

    /**
     * Email can not be empty, so if it's empty, it means there wasn't an email field in the form. In that case, remove
     * email from the differences list.
     * The function always returns true.
     *
     * @param array $data Shipping address fields that were changed
     *
     * @return bool
     */
    protected function postprocessDifferenceBillingAddressFields(array &$data): bool
    {
        if (empty($data['email'])) {
            unset($data['email']);
        }
        return true;
    }

    /**
     * Postprocess fingerprint difference parameter.
     * Return false if this param should be removed from event-updateCart params list.
     *
     * @param array $data New payment method ID
     *
     * @return boolean
     */
    protected function postprocessDifferencePaymentMethodId($data)
    {
        $oldPaymentMethod = null;

        // Get old payment method
        if (!empty($this->initialCartFingerprint)) {
            $oldPaymentMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->find($this->initialCartFingerprint['paymentMethodId']);
        }

        $newPaymentMethod = $this->getCart()->getPaymentMethod();

        return ($newPaymentMethod && $newPaymentMethod->isCheckoutUpdateActionRequired())
            || ($oldPaymentMethod && $oldPaymentMethod->isCheckoutUpdateActionRequired());
    }

    /**
     * isCartProcessed
     *
     * @return boolean
     */
    protected function isCartProcessed()
    {
        return $this->getCart()->isProcessed() || $this->getCart()->isQueued();
    }

    /**
     * Get or create cart profile
     *
     * @return \XLite\Model\Profile
     * @throws \Exception
     */
    protected function getCartProfile()
    {
        $profile = null;

        try {
            \XLite\Core\Database::getEM()->transactional(function (\Doctrine\ORM\EntityManagerInterface $em) use (&$profile) {
                $cart = $this->getCart();
                if ($cart->isManaged()) {
                    $em->lock($cart, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
                    $em->refresh($cart);
                }

                $profile = $cart->getProfile();
                if (!$profile && $cart->isManaged()) {
                    $profile = new \XLite\Model\Profile();
                    $profile->setLogin('');
                    $profile->setOrder($cart);
                    $profile->setAnonymous(true);

                    $cart->setProfile($profile);
                    $em->persist($profile);
                }
            });
        } catch (\Exception $e) {
            $this->getLogger()->error(
                'Failure to create anonymous profile for cart ',
                [
                    'cart-id' => $this->getCart()->getUniqueIdentifier(),
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTrace(),
                ]
            );

            if (!\XLite\Core\Database::getEM()->isOpen()) {
                throw $e;
            }
        }

        return $profile;
    }

    /**
     * Check - need use secure protocol or not
     *
     * @return boolean
     */
    public function needSecure()
    {
        return parent::needSecure()
            || (!$this->isHTTPS()) && $this->isFullCustomerSecurity();
    }

    /**
     * Check if the any customer script must be redirected to HTTPS
     *
     * @return boolean
     */
    protected function isFullCustomerSecurity()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    // {{{ Clean URLs related routines

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (
            LC_USE_CLEAN_URLS
            && !\XLite::isCleanURL()
            && !$this->isAJAX()
            && !$this->isRedirectNeeded()
            && $this->isRedirectToCleanURLNeeded()
        ) {
            $this->performRedirectToCleanURL();
        }

        if (
            !$this->isAJAX()
            && !$this->isRedirectNeeded()
        ) {
            \XLite\Core\Session::getInstance()->continueShoppingURL = $this->getAllParams();
        }
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return boolean
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return isset(\XLite\Model\Repo\CleanURL::getConfigCleanUrlAliases()[$this->getTarget()])
            || preg_match(
                '/\/cart\.php/Si',
                \Includes\Utils\ArrayManager::getIndex(
                    \XLite\Core\Request::getInstance()->getServerData(),
                    'REQUEST_URI'
                )
            );
    }

    /**
     * Redirect to clean URL
     *
     * @return void
     */
    protected function performRedirectToCleanURL()
    {
        $data = \XLite\Core\Request::getInstance()->getGetData();

        if (isset($data['url'])) {
            unset($data['url']);
        }

        $target = $this->getTarget();

        if ($target === \XLite::TARGET_DEFAULT) {
            $target = '';
            unset($data['target']);
        } else {
            unset($data['target']);
        }

        $url = \XLite\Core\Converter::buildFullURL($target, '', $data);

        $ttl         = 86400;
        $expiresTime = gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT';

        header("Cache-Control: max-age=$ttl, must-revalidate");
        header("Expires: $expiresTime");

        $this->redirect($url, 301);
    }

    // }}}

    // {{{ Getters

    /**
     * Get address fields
     *
     * @return array
     */
    public function getAddressFields()
    {
        if (!isset($this->addressFields)) {
            $result = [];

            foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
                $result[$field->getServiceName()] = [
                    \XLite\View\Model\Address\Address::SCHEMA_CLASS            => $field->getSchemaClass(),
                    \XLite\View\Model\Address\Address::SCHEMA_LABEL            => $field->getName(),
                    \XLite\View\Model\Address\Address::SCHEMA_REQUIRED         => $field->getRequired(),
                    \XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES => [
                        \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                    ],
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS      => 'address-' . $field->getServiceName()
                ];
            }

            $this->addressFields = $this->getFilteredSchemaFields($result);
        }

        return $this->addressFields;
    }

    /**
     * Filter schema fields
     *
     * @param array $fields Schema fields to filter
     *
     * @return array
     */
    protected function getFilteredSchemaFields($fields)
    {
        if (!isset($fields['country_code'])) {
            // Country code field is disabled
            // We need leave oonly one state field: selector or text field

            $deleteStateSelector = true;

            $address = new \XLite\Model\Address();

            if ($address && $address->getCountry() && $address->getCountry()->hasStates()) {
                $deleteStateSelector = false;
            }

            if ($deleteStateSelector && isset($fields['state_id'])) {
                unset($fields['state_id']);

                if (isset($fields['custom_state'])) {
                    $fields['custom_state']['additionalClass'] = 'single-state-field';
                }
            } elseif (!$deleteStateSelector && isset($fields['custom_state'])) {
                unset($fields['custom_state']);

                if (isset($fields['state_id'])) {
                    $fields['state_id'][\XLite\View\FormField\Select\State::PARAM_COUNTRY] = $address->getCountry()->getCode();
                    $fields['state_id']['additionalClass']                                 = 'single-state-field';
                }
            }
        }

        return $fields;
    }

    /**
     * Get field value
     *
     * @param string               $fieldName    Field name
     * @param \XLite\Model\Address $address      Field name
     * @param boolean              $processValue Process value flag OPTIONAL
     *
     * @return string
     */
    public function getFieldValue($fieldName, \XLite\Model\Address $address, $processValue = false)
    {
        $result = '';

        if ($address !== null) {
            $methodName = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($fieldName);

            // $methodName assembled from 'get' + camelized $fieldName
            $result = $address->$methodName();

            if ($result && $processValue !== false) {
                switch ($fieldName) {
                    case 'state_id':
                        $result = $address->getCountry()->hasStates()
                            ? $address->getState()->getState()
                            : null;
                        break;

                    case 'custom_state':
                        $result = $address->getCountry()->hasStates()
                            ? null
                            : $result;
                        break;

                    case 'country_code':
                        $result = $address->getCountry()->getCountry();
                        break;

                    case 'type':
                        $result = $address->getTypeName();
                        break;

                    default:
                }
            }
        }

        return $result;
    }

    // }}}

    /**
     * Return current product Id
     *
     * @return integer
     */
    public function getProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * Check - is service controller or not
     *
     * @return boolean
     */
    protected function isServiceController()
    {
        return false;
    }

    /**
     * Get default max product image width
     *
     * @param boolean $width If true method will return width else - height
     * @param string  $model Model class name
     * @param string  $code  Image sizes code, see \XLite\Logic\ImageResize\Generator::defineImageSizes()
     *
     * @return integer
     */
    public function getDefaultMaxImageSize($width = true, $model = null, $code = null)
    {
        if (is_null($model)) {
            $model = \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT;
        }

        if (is_null($code)) {
            $code = 'Default';
        }

        $resizeData = \XLite\Logic\ImageResize\Generator::getImageSizes($model, $code);

        $id = intval(!$width);

        // $resizeData[0] - width, $resizeData[1] - height
        return $resizeData[$id] ?? 0;
    }

    /**
     * Makes given address as selected on current cart.
     * Throws core events "selectCartAddress" and "updateCart".
     *
     * @param  [type]  $atype               Address type (billing\shipping) short tag
     * @param  [type]  $addressId           Address id
     * @param boolean $hasEmptyFields      If true, sends updateCart event even if addressId hasn't changed
     * @param boolean $preserveSameAddress If true and shipping\billing addresses are the same, new address will be applied to both addresses; if false, only the address of given type will change.
     */
    protected function selectCartAddress($atype, $addressId, $hasEmptyFields = false, $preserveSameAddress = true)
    {
        if ($atype != \XLite\Model\Address::SHIPPING && $atype != \XLite\Model\Address::BILLING) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Address type has wrong value');
        } elseif (!$addressId) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Address is not selected');
        } else {
            $address = \XLite\Core\Database::getRepo('XLite\Model\Address')->find($addressId);

            if (!$address) {
                // Address not found
                $this->valid = false;
                \XLite\Core\TopMessage::addError('Address not found');
            } elseif (
                $atype == \XLite\Model\Address::SHIPPING
                && $this->getCart()->getProfile()->getShippingAddress()
                && $address->getAddressId() == $this->getCart()->getProfile()->getShippingAddress()->getAddressId()
            ) {
                if ($hasEmptyFields) {
                    \XLite\Core\Event::updateCart(
                        [
                            'shippingAddressId' => $address->getAddressId(),
                        ]
                    );
                }
            } elseif (
                $atype == \XLite\Model\Address::BILLING
                && $this->getCart()->getProfile()->getBillingAddress()
                && $address->getAddressId() == $this->getCart()->getProfile()->getBillingAddress()->getAddressId()
            ) {
                if ($hasEmptyFields) {
                    \XLite\Core\Event::updateCart(
                        [
                            'billingAddressId' => $address->getAddressId(),
                        ]
                    );
                }
            } else {
                if ($atype == \XLite\Model\Address::SHIPPING) {
                    $old          = $this->getCart()->getProfile()->getShippingAddress();
                    $andAsBilling = false;
                    if ($old) {
                        $old->setIsShipping(false);
                        $andAsBilling = \XLite\Core\Session::getInstance()->same_address;
                        if ($old->getIsWork() && !$andAsBilling) {
                            $this->getCart()->getProfile()->getAddresses()->removeElement($old);
                            \XLite\Core\Database::getEM()->remove($old);
                        } elseif ($andAsBilling && $preserveSameAddress) {
                            $old->setIsBilling(false);
                        }
                    } elseif (!$this->getCart()->getProfile()->getBillingAddress()) {
                        $andAsBilling = true;
                    }

                    $address->setIsShipping(true);
                    if ($andAsBilling && $preserveSameAddress) {
                        $address->setIsBilling($andAsBilling);
                    }
                } else {
                    $old           = $this->getCart()->getProfile()->getBillingAddress();
                    $andAsShipping = false;
                    if ($old) {
                        $old->setIsBilling(false);
                        $andAsShipping = \XLite\Core\Session::getInstance()->same_address;
                        if ($old->getIsWork() && !$andAsShipping) {
                            $this->getCart()->getProfile()->getAddresses()->removeElement($old);
                            \XLite\Core\Database::getEM()->remove($old);
                        } elseif ($andAsShipping && $preserveSameAddress) {
                            $old->setIsShipping(false);
                        }
                    } elseif (!$this->getCart()->getProfile()->getShippingAddress()) {
                        $andAsShipping = true;
                    }

                    $address->setIsBilling(true);
                    if ($andAsShipping && $preserveSameAddress) {
                        $address->setIsShipping($andAsShipping);
                    }
                }

                \XLite\Core\Session::getInstance()->same_address = $this->getCart()->getProfile()->isEqualAddress();

                \XLite\Core\Event::selectCartAddress(
                    [
                        'type'      => $atype,
                        'addressId' => $address->getAddressId(),
                        'same'      => $this->getCart()->getProfile()->isSameAddress(),
                        'fields'    => $address->serialize(),
                    ]
                );

                \XLite\Core\Database::getEM()->flush();

                $this->updateCart();
            }
        }
    }

    /**
     * Mark controller run thread as access denied
     *
     * @return void
     */
    protected function markAsAccessDenied()
    {
        \XLite\Core\Request::getInstance()->fromURL = $this->buildURL(
            $this->getTarget(),
            $this->getAction(),
            \XLite\Core\Request::getInstance()->getData()
        );

        parent::markAsAccessDenied();
    }
}
