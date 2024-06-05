<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Core;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XLite\Base\Singleton;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Model\Payment\Method;

/**
 * XPayments connector tabs and settings
 *
 */
class Settings extends Singleton
{
    /**
     * Tabs/pages
     */
    const PAGE_PAYMENT_METHODS = 'payment_methods';
    const PAGE_CONNECTION      = 'connection';
    const PAGE_ZERO_AUTH       = 'zero_auth';
    const PAGE_WELCOME         = 'welcome';

    /**
     * Results of the test connection operation
     */
    const RESULT_FAILED = 0;
    const RESULT_SUCCESS = 1;
    const RESULT_API_VERSION_CHANGED = 2;

    const XP_ALLOWED_PREFIX = 'XPayments.Allowed';
    const XP_MODULE_NAME = 'Qualiteam_SkinActXPaymentsConnector';

    /**
     * Default error text
     */
    const TXT_CONNECT_FAILED = 'Test transaction failed. Please check the X-Payment Connector settings and try again.
                                If all options is ok review your X-Payments settings and make sure you have properly
                                defined shopping cart properties.';

    /**
     * How often load list of allowed modules from site
     */
    const ALLOWED_MODULES_CACHE_TTL = 86400;

    /**
     * Validation regex for serialized bundle
     */
    const BUNDLE_VALIDATION_REGEX = '/a:[56]:{s:8:"store_id";s:\d+:"[0-9a-z]+";s:3:"url";s:\d+:"[^"]+";s:10:"public_key";s:\d+:"-----BEGIN CERTIFICATE-----[^"]+-----END CERTIFICATE-----";s:11:"private_key";s:\d+:"-----BEGIN [A-Z ]*PRIVATE KEY-----[^"]+-----END [A-Z ]*PRIVATE KEY-----";s:20:"private_key_password";s:32:".{32}";(s:9:"server_ip";s:\d+:"[0-9a-fA-F\.:]*";)?}/s';

    /**
     * Default admin order of payment method
     */
    const DEFAULT_ADMIN_ORDER_ID = 0;

    /**
     * List of API versions
     */
    public $apiVersions = array(
        '1.9',
        '1.8',
        '1.7',
        '1.6',
        '1.5',
        '1.4',
        '1.3',
        '1.2',
        '1.1',
    );

    /**
     * List oof module names with associated class
     */
    public $modulesMap = [
        'ANZ eGate'                                         => 'XPay_Module_ANZeGate',
        'American Express Web-Services API Integration'     => 'XPay_Module_Amex',
        'Authorize.Net AIM'                                 => 'XPay_Module_AuthorizeNet',
        'Authorize.Net CIM'                                 => 'XPay_Module_AuthorizeNetCim',
        'Authorize.Net AIM (XML API)'                       => 'XPay_Module_AuthorizeNetXML',
        'Bambora (Beanstream)'                              => 'Xpay_Module_Bambora',
        'Beanstream (legacy API)'                           => 'XPay_Module_Bean',
        'Bendigo Bank'                                      => 'XPay_Module_BendigoBank',
        'BillriantPay'                                      => 'XPay_Module_BillriantPay',
        'BluePay'                                           => 'XPay_Module_BluePay',
        'BlueSnap Payment API (XML)'                        => 'XPay_Module_BlueSnap',
        'Braintree'                                         => 'XPay_Module_Braintree',
        'Caledon'                                           => 'XPay_Module_Caledon',
        'Cardinal Commerce Centinel'                        => 'XPay_Module_CardinalCommerce',
        'Chase Paymentech'                                  => 'XPay_Module_Chase',
        'CommWeb - Commonwealth Bank'                       => 'XPay_Module_CommWeb',
        'CyberSource - SOAP toolkit API'                    => 'XPay_Module_CyberSourceSOAP',
        'DIBS'                                              => 'XPay_Module_Dibs',
        'DirectOne - Direct Interface'                      => 'XPay_Module_DirectOne',
        'eProcessing Network - Transparent Database Engine' => 'XPay_Module_EProcessingTDE',
        'SecurePay Australia'                               => 'XPay_Module_ESec',
        'eSelect DirectPost'                                => 'XPay_Module_ESelect',
        'ECHO NVP'                                          => 'XPay_Module_Echo',
        'Elavon (Realex API)'                               => 'XPay_Module_Elavon',
        'ePDQ MPI XML (Phased out)'                         => 'XPay_Module_EpdqXML',
        'eWay Realtime Payments XML'                        => 'XPay_Module_EwayXML',
        'eWAY Rapid - Direct Connection'                    => 'XPay_Module_EwayRapid',
        '5th Dimension Gateway'                             => 'XPay_Module_FifthDimensionGateway',
        'First Data Global Gateway e4(SM) Web Service API'  => 'XPay_Module_FirstDataE4',
        'Global Iris'                                       => 'XPay_Module_GlobalIris',
        'GoEmerchant - XML Gateway API'                     => 'XPay_Module_GoEmerchant',
        'HeidelPay'                                         => 'XPay_Module_HeidelPay',
        'Innovative Gateway'                                => 'XPay_Module_InnovativeGateway',
        'Intuit QuickBooks Payments (Legacy QBMS API)'      => 'XPay_Module_Intuit',
        'iTransact XML'                                     => 'XPay_Module_ItransactXML',
        'Meritus Web Host'                                  => 'XPay_Module_Meritus',
        'NAB - National Australia Bank'                     => 'XPay_Module_NAB',
        'Netevia'                                           => 'XPay_Module_Netevia',
        'NetRegistry'                                       => 'XPay_Module_NetRegistry',
        'Netbilling - Direct Mode 3.1'                      => 'XPay_Module_Netbilling',
        'NMI (Network Merchants Inc.)'                      => 'XPay_Module_NMI',
        'Ogone/ePDQ e-Commerce'                             => 'XPay_Module_Ogone',
        'PayGate Korea'                                     => 'XPay_Module_PayGate',
        'Payflow Pro'                                       => 'XPay_Module_PayflowPro',
        'PayPal Payments Pro (PayPal API)'                  => 'XPay_Module_PaypalWPPDirectPayment',
        'PayPal Payments Pro (Payflow API)'                 => 'XPay_Module_PaypalWPPPEDirectPayment',
        'PSiGate XML API'                                   => 'XPay_Module_PsiGateXML',
        'QuantumGateway - Transparent QGWdatabase Engine'   => 'XPay_Module_QuantumGateway',
        'QuantumGateway - XML Requester'                    => 'XPay_Module_QuantumGatewayXML',
        'Intuit QuickBooks Payments'                        => 'XPay_Module_QuickBooksPayments',
        'QuickPay'                                          => 'XPay_Module_QuickPay',
        'Worldpay Corporate Gateway - Direct Model'         => 'XPay_Module_RBSGlobalGatewayDirect',
        'Global Payments (ex. Realex)'                      => 'XPay_Module_Realex',
        'Sage Pay Go - Direct Interface'                    => 'XPay_Module_SagePayDirect',
        'SecurePay'                                         => 'XPay_Module_Securepay',
        'SkipJack'                                          => 'XPay_Module_SkipJack',
        'Paya (ex. Sage Payments US)'                       => 'XPay_Module_SageUs',
        'Simplify Commerce by MasterCard'                   => 'XPay_Module_SimplifyCommerce',
        'Suncorp'                                           => 'XPay_Module_Suncorp',
        'USA ePay - Transaction Gateway API'                => 'XPay_Module_USAePay',
        'Virtual Merchant - Merchant Provided Form'         => 'XPay_Module_VirtualMerchantMPF',
        'WebXpress'                                         => 'XPay_Module_WebXpress',
        'Worldpay Total US'                                 => 'XPay_Module_WorldpayTotalUS',
        'Worldpay US'                                       => 'XPay_Module_WorldpayUs',
    ];

    /**
     * List of configuration fields separated by pages
     */
    public $pageFields = array(

        self::PAGE_WELCOME => array(),

        self::PAGE_PAYMENT_METHODS => array(),

        self::PAGE_CONNECTION => array(
            'xpc_shopping_cart_id',
            'xpc_xpayments_url',
            'xpc_public_key',
            'xpc_private_key',
            'xpc_private_key_password',
            'xpc_currency',
            'xpc_api_version',
            'xpc_use_iframe',
        ),

        self::PAGE_ZERO_AUTH => array(
            'xpc_zero_auth_method_id',
            'xpc_zero_auth_amount',
            'xpc_zero_auth_description',
        ),

    );

    /**
     * Map fields
     *
     * @var array
     */
    protected $mapFields = array(
        'store_id'             => 'xpc_shopping_cart_id',
        'url'                  => 'xpc_xpayments_url',
        'public_key'           => 'xpc_public_key',
        'private_key'          => 'xpc_private_key',
        'private_key_password' => 'xpc_private_key_password',
    );

    /**
     * Required fields
     *
     * @var array
     */
    protected $requiredFields = array(
        'store_id',
        'url',
        'public_key',
        'private_key',
        'private_key_password',
    );

    /**
     * Get all pages
     *
     * @return array
     */
    public static function getAllPages()
    {
        if (XPaymentsClient::getInstance()->isModuleConfigured()) {

            if (static::hasSaveCardsPaymentMethods()) {

                // Spaghetti-code here is the simplest way to keep the order of pages

                $pages = array(
                    static::PAGE_PAYMENT_METHODS => static::t('Payment methods'),
                    static::PAGE_CONNECTION      => static::t('Connection'),
                    static::PAGE_ZERO_AUTH       => static::t('Save credit card setup'),
                    static::PAGE_WELCOME         => static::t('Welcome'),
                );

            } else {

                $pages = array(
                    static::PAGE_PAYMENT_METHODS => static::t('Payment methods'),
                    static::PAGE_CONNECTION      => static::t('Connection'),
                    static::PAGE_WELCOME         => static::t('Welcome'),
                );
            }

        } else {

            $pages = array(
                static::PAGE_WELCOME         => static::t('Welcome'),
                static::PAGE_CONNECTION      => static::t('Connection'),
            );
        }

        return $pages;
    }

    /**
     * Get page
     *
     * @return array
     */
    public function getPage($page)
    {
        return constant('self::' . $page);
    }

    /**
     * Check if page is valid
     *
     * @param string $page Page to check
     *
     * @return bool 
     */
    public static function isPageValid($page)
    {
        return in_array(strval($page), array_keys(self::getAllPages()));
    }

    /**
     * Check - is payment configurations imported early or not
     *
     * @param string $processor Payment processor to check
     *
     * @return boolean
     */
    public static function hasPaymentMethods($processor = 'XPayments')
    {
        return 0 < count(static::getPaymentMethods($processor));
    }

    /**
     * Get payment methods
     *
     * @return array
     */
    public static function getPaymentMethods($processor = 'XPayments')
    {
        $cnd = new CommonCell();
        $cnd->class = 'Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\\' . $processor;
        $cnd->fromMarketplace = 0;

        return Database::getRepo('XLite\Model\Payment\Method')->search($cnd);
    }

    /**
     * Check if at least one method supports tokenization
     *
     * @return array
     */
    public static function hasSaveCardsPaymentMethods()
    {
        $result = false;

        foreach (static::getPaymentMethods() as $pm) {
            if ($pm->getSetting('canSaveCards') == 'Y') {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get default page
     *
     * @return string
     */
    public function getDefaultPage()
    {
        return XPaymentsClient::getInstance()->isModuleConfigured()
            ? static::PAGE_PAYMENT_METHODS
            : static::PAGE_WELCOME;
    }

    /**
     * Get list of fields for page
     *
     * @param string $page Page name
     *
     * @return array
     */
    public function getFieldsForPage($page = '')
    {
        $fields = isset($this->pageFields[$page])
            ? $this->pageFields[$page]
            : array();

        // Remove currency setting for API 1.3 and higher
        if (
            static::PAGE_CONNECTION == $page
            && version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.3') >= 0
        ) {

            $key = array_search('xpc_currency', $fields);

            if ($key !== false) {
                unset($fields[$key]);
            }
        }

        return $fields;
    }

    /**
     * Test connection
     *
     * @param bool $silent Silent check or not
     *
     * @return bool 
     */
    public function testConnection($silent = true)
    {
        $result = self::RESULT_FAILED;

        $client = XPaymentsClient::getInstance();

        if ($silent) {

            // Test connection using API version from settings
            $response = $client->requestTest();

            if ($response->isSuccess()) {
                $result = self::RESULT_SUCCESS;
            }

        } else {

            foreach ($this->apiVersions as $version) {

                $response = $client->requestTest($version);

                if ($response->isSuccess()) {

                    if (Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version !== $version) {

                        $apiVersionSetting = Database::getRepo('XLite\Model\Config')
                            ->findOneBy(array('name' => 'xpc_api_version', 'category' => 'Qualiteam\SkinActXPaymentsConnector'));

                        Database::getRepo('XLite\Model\Config')->update(
                            $apiVersionSetting,
                            array('value' => $version)
                        );

                        // Update config data
                        Config::updateInstance();

                        $result = self::RESULT_API_VERSION_CHANGED;

                    } else {

                        $result = self::RESULT_SUCCESS;
                    }

                    TopMessage::addInfo(
                        'Test transaction completed successfully for API version X',
                        array('version' => $version)
                    );

                    break;
                }
            }

            if (self::RESULT_FAILED === $result) {

                TopMessage::addWarning(self::TXT_CONNECT_FAILED);

                if ($response->getError()) {
                    TopMessage::addError($response->getError());
                }
            }
        }

        return $result;
    }

    // {{{ Deploy configuration

    /**
     * Check and deploy configuration
     *
     * @param string $deployConfig String containing a deployment configuration
     *
     * @return string
     */
    public function deployConfiguration($deployConfig)
    {
        $xpcConfig = $this->getConfiguration($deployConfig);

        $errorMsg = '';

        if (true === $this->checkDeployConfiguration($xpcConfig)) {

            $this->setConfiguration($xpcConfig);

            Config::updateInstance();

            $connectResult = $this->testConnection(false);

            if (self::RESULT_FAILED === $connectResult) {

                $errorMsg = 'Configuration has been deployed, but X-Cart is unable to connect to X-Payments';

            } else {

                $this->importPaymentMethods($connectResult);
            }

        } else {
            $errorMsg = 'Your configuration string is not correct';
        }

        return $errorMsg;
    }

    /**
     * Get configuration array from configuration deployment path
     *
     * @return array
     */
    protected function getConfiguration($deployConfig)
    {
        $deployConfig = base64_decode($deployConfig);

        if (preg_match(static::BUNDLE_VALIDATION_REGEX, $deployConfig)) {
            // It is serialized data and it contains only the configuration fields
            $result = unserialize($deployConfig);
        } else {
            // Try modern JSON bundle
            $result = json_decode($deployConfig, true);
        }

        if (!$result || !is_array($result)) {
            $result = [];
        }

        return $result;
    }

    /**
     * Check if the deploy configuration is correct array
     *
     * @param array $configuration Configuration array
     *
     * @return boolean
     */
    protected function checkDeployConfiguration($configuration)
    {
        return is_array($configuration)
            && ($this->requiredFields === array_intersect(array_keys($configuration), $this->requiredFields));
    }

    /**
     * Store configuration array into DB
     *
     * @param array $configuration Configuration array
     *
     * @return void
     */
    protected function setConfiguration($configuration)
    {
        foreach ($this->mapFields as $origName => $dbName) {

            if (!isset($configuration[$origName])) {
                continue;
            }

            $setting = Database::getRepo('XLite\Model\Config')
                ->findOneBy(array('name' => $dbName, 'category' => 'Qualiteam\SkinActXPaymentsConnector'));

            Database::getRepo('XLite\Model\Config')->update(
                $setting,
                array('value' => $configuration[$origName])
            );
        }
    }

    // }}}

    /**
     * Get Human Readable name for 3-D Secure type
     *
     * @return string 
     */
    protected static function get3DSecureType($secure3d)
    {
        $secure3dType = '';

        switch ($secure3d) {

            case '0' :
                $secure3dType = 'Not supported';
                break;

            case '1' :
                $secure3dType = 'via Cardinal Commerce';
                break;

            case '2' :
            case '3' :
                $secure3dType = 'Internal';
                break;

            default:

        }

        return $secure3dType;
    }

    // {{{ Import payment methods

    /**
     * Detect X-Payments' module class
     *
     * @param array $moduleData Module data received from X-Payments or from X-Cart site
     *
     * @return array
     */
    protected function detectModuleClass($moduleData = array())
    {
        if (isset($moduleData['class'])) {

            $result = $moduleData['class'];

        } elseif (
            isset($moduleData['moduleName'])
            && array_key_exists($moduleData['moduleName'], $this->modulesMap)
        ) {

            $result = $this->modulesMap[$moduleData['moduleName']];

        } elseif (isset($moduleData['id'])) {

            $result = $moduleData['id'];

        } else {

            $result = '';

        }

        return $result;

    }

    /**
     * Check that existing payment method is in the list of the imported methods. Mark that in the list 
     *
     * @param Method $paymentMethod       Payment method
     * @param array                       $list List of the imported payment methods
     *
     * @return bool
     */
    protected function checkMethodInImportedList(Method $paymentMethod, &$list)
    {
        $result = false;

        if (0 === strpos($paymentMethod->getServiceName(), self::XP_ALLOWED_PREFIX)) {
            $xpModuleClass = str_replace(self::XP_ALLOWED_PREFIX, '', $paymentMethod->getServiceName());
        } else {
            $xpModuleClass = str_replace('XPayments.', '', $paymentMethod->getServiceName());
        }

        foreach ($list as $key => $data) {
            if (
                (
                    (!empty($data['class']) && $xpModuleClass == $data['class'])
                    ||
                    $paymentMethod->getSetting('moduleName') == $data['moduleName']
                )
                && $paymentMethod->getSetting('id') == $data['id'] 
            ) {
                $result = true;

                $list[$key]['paymentMethodId'] = $paymentMethod->getMethodId();
                break;
            } 
        }

        return $result;
    }

    /**
     * Import payment methods from X-Payments and return error or warning message (if any)
     *
     * @param int $connectResult Connection result
     *
     * @return void
     */
    public function importPaymentMethods($connectResult)
    {
        XPaymentsClient::getInstance()->fixSavedCardMethod();

        $list = XPaymentsClient::getInstance()->requestPaymentMethods();

        if (is_array($list) && !empty($list)) {

            $pmNames = [];

            $pmsToRemove = [];

            foreach (static::getPaymentMethods() as $pm) {

                if (!$this->checkMethodInImportedList($pm, $list)) {

                    $pmNames[] = $pm->getName();

                    $pmsToRemove[$pm->getServiceName()][] = $pm;
                }
            }

            $allPmsNeedToRemove = false;

            foreach ($pmsToRemove as $key => $pms) {

                foreach ($list as $item) {
                    if (self::XP_ALLOWED_PREFIX . '.' . $item['class'] == $key) {
                        $allPmsNeedToRemove = true;
                        break;
                    }
                }

                foreach ($pms as $keyInner => $pm) {
                    if (
                        0 == $keyInner
                        && !$allPmsNeedToRemove
                    ) {
                        $pm->makeMethodFake();
                    } else {
                        Database::getEM()->remove($pm);
                    }
                }
            }

            if (self::RESULT_API_VERSION_CHANGED === $connectResult) {

                $carts = Database::getRepo('XLite\Model\Cart')->findByPaymentMethodNames($pmNames);
  
                // TODO: Might be slow. Consider reworking for the faster operating.
                ZeroAuth::cleanupFakeCarts($carts);
 
                foreach ($carts as $cart) {
                    $cart->unsetPaymentMethod();
                }

                XPaymentsClient::getInstance()->clearAllInitData();
            }

            foreach ($list as $settings) {

                if (!isset($settings['paymentMethodId'])) {

                    $xpModuleClass = $this->detectModuleClass($settings);

                    $pm = Database::getRepo('XLite\Model\Payment\Method')
                        ->findOneBy([
                            'service_name' => self::XP_ALLOWED_PREFIX . '.' . $xpModuleClass,
                            'fromMarketplace' => true,
                        ]);

                    if ($pm) {
                        // Make fake PM the real one instead of adding new PM
                        $pm->setFromMarketplace(false);
                        $pm->setName($this->getPaymentMethodName($settings['name']));
                        $pm->setAdded(true);
                        $pm->setEnabled(true);

                        Database::getEM()->flush();

                    } else {
                        // Create new payment method
                        $pm = new Method;
                        Database::getEM()->persist($pm);

                        /** @var Method $realPm */
                        $realPm = Database::getRepo('XLite\Model\Payment\Method')
                            ->findOneBy([
                                'service_name' => self::XP_ALLOWED_PREFIX . '.' . $xpModuleClass,
                                'fromMarketplace' => false,
                            ]);

                        $pm->setClass(XPayments::class);
                        $pm->setServiceName(self::XP_ALLOWED_PREFIX . '.' . $xpModuleClass);
                        $pm->setName($this->getPaymentMethodName($settings['name']));
                        $pm->setType(Method::TYPE_CC_GATEWAY);
                        $pm->setAdded(true);
                        $pm->setEnabled(true);
                        $pm->setFromMarketplace(false);

                        if ($realPm) {
                            //$pm->setIconURL($realPm->getIconURL());
                            $pm->setCountries($realPm->getCountries());
                            $pm->setExCountries($realPm->getExCountries());
                            $pm->setOrderby($realPm->getOrderby());
                            $pm->setAdminOrderby($realPm->getAdminOrderby());
                        }

                        // Tokenization is disabled by default
                        $pm->setSetting('saveCards', 'N');
                    }

                } else {

                    // Use existing payment method
                    $pm = Database::getRepo('XLite\Model\Payment\Method')->find($settings['paymentMethodId']);
                    $pm->setName($this->getPaymentMethodName($settings['name']));
                }

                $this->setPaymentMethodSettings($pm, $settings);
            }

            Database::getEM()->flush();

            TopMessage::addInfo('Payment methods have been imported successfully');

        } elseif (is_array($list)) {

            $pmsToRemove = [];

            foreach (static::getPaymentMethods() as $pm) {
                $pmsToRemove[$pm->getServiceName()][] = $pm;
            }

            foreach ($pmsToRemove as $pms) {
                foreach ($pms as $key => $pm) {
                    if (0 == $key) {
                        $pm->makeMethodFake();
                    } else {
                        Database::getEM()->remove($pm);
                    }
                }
            }

            if (self::RESULT_API_VERSION_CHANGED === $connectResult) {
                ZeroAuth::cleanupFakeCarts();
                XPaymentsClient::getInstance()->clearAllInitData();
            }

            TopMessage::addWarning('There are no payment configurations for this store.');

        } else {

            TopMessage::addError('Error had occured during the requesting of payment methods from X-Payments. See log files for details.');

        }
    }

    /**
     * Set payment method settings
     *
     * @param Method $pm       Payment method
     * @param array                       $settings Settings
     *
     * @return void
     */
    protected function setPaymentMethodSettings(Method $pm, array $settings)
    {
        foreach ($settings as $k => $v) {

            if (is_array($v)) {

                $this->setPaymentMethodSettings($pm, $v);

            } elseif ('currency' == $k) {

                $currency = Database::getRepo('XLite\Model\Currency')->findOneByCode($v);

                if (is_object($currency)) {
                    $pm->setSetting($k, $currency->getCurrencyId());
                } else {
                    $pm->setSetting($k, '840'); // USD
                }


            } else {
                $pm->setSetting($k, $v);
            }
        }

        // Consider that all methods can save cards for old X-Payments
        if (version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.3') < 0) {
            $pm->setSetting('canSaveCards', 'Y');
        }
    }

    /**
     * Get the name of an imported payment method
     *
     * @param string $origName Original payment method name
     *
     * @return string
     */
    public function getPaymentMethodName($origName)
    {
        if (preg_match('/\b(credit|debit|card)\b/i', $origName)) {
            $newName = $origName;
        } else {
            $newName = 'Credit or debit card via ' . $origName;
        }

        return $newName;
    }

    // }}}
}
