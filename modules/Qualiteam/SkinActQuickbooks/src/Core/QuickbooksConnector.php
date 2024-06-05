<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Core;

use XLite\Core\Config;
use Qualiteam\SkinActQuickbooks\Main as QBMain;
use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksCustomers;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrders;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors;
use XLite\Model\Base\Surcharge;

/**
 * QuickbooksConnector
 */
class QuickbooksConnector extends \XLite\Base\Singleton
{
    /**
     * Quickbooks SQL connection string
     * 
     * @var type string
     */
    private static $dsn = '';
    
    /**
     * Query limit (per request)
     */
    public static $queryLimit = 100;
    
    /**
     * Load SDK
     * 
     * @return void
     */
    public static function loadSDK()
    {
        $path = LC_DIR_MODULES . 'Qualiteam' . LC_DS . 'SkinActQuickbooks'
            . LC_DS . 'sdk' . LC_DS . 'QuickBooks.php';
        
        require_once $path;
    }
    
    /**
     * Get DSN
     * 
     * @return string
     */
    public static function getDSN()
    {
        if (!self::$dsn) {
            $em = \XLite\Core\Database::getEM();
            if ($em && ($conn = $em->getConnection())) {
                $params = $conn->getParams();
                $sqlUser = $params['user'];
                $sqlPass = $params['password'];
                $sqlHost = $params['host'];
                $sqlDb = $params['dbname'];
            }
            self::$dsn = "mysqli://$sqlUser:$sqlPass@$sqlHost/$sqlDb";
        }
    
        return self::$dsn;
    }
    
    /**
     * Get QB username
     * 
     * @return string
     */
    public static function getUsername()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_user;
    }
    
    /**
     * Get QB password
     * 
     * @return string
     */
    public static function getPassword()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_pass;
    }
    
    /**
     * Is QB sync enabled
     * 
     * @return boolean
     */
    public static function isSyncEnabled()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_enable_sync;
    }
    
    /**
     * Allow to add products to QuickBooks
     * 
     * @return boolean
     */
    public static function allowAddProducts()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_add;
    }
    
    /**
     * Update product prices
     * 
     * @return boolean
     */
    public static function updateProductPrices()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_update_prices;
    }
    
    /**
     * Unlink product when "QuickBooks Item Name/Number" is set empty
     * 
     * @return boolean
     */
    public static function unlinkProductWhenFullnameEmpty()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_unlink_empty_fname;
    }
    
    /**
     * Product Income Account
     * 
     * @return string
     */
    public static function productIncomeAccount()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_income_acc;
    }
    
    /**
     * Product COGS Account
     * 
     * @return string
     */
    public static function productCogsAccount()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_cogs_acc;
    }
    
    /**
     * Product Asset Account
     * 
     * @return string
     */
    public static function productAssetAccount()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_products_asset_acc;
    }
    
    /**
     * Order Class
     * 
     * @return string
     */
    public static function orderClassRef()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_class_ref;
    }
    
    /**
     * Order Template
     * 
     * @return string
     */
    public static function orderTemplateRef()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_template_ref;
    }
    
    /**
     * Order Discount
     * 
     * @return string
     */
    public static function orderDiscountRef()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_discount_ref;
    }
    
    /**
     * Order Shipping
     * 
     * @return string
     */
    public static function orderShippingRef()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_shipping_ref;
    }
    
    /**
     * Send emails about orders import errors
     * 
     * @return string
     */
    public static function sendEmailOrdersErrors()
    {
        return Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_import_errors_email;
    }
    
    /**
     * Initialize QB service tables
     * 
     * @return void
     */
    public static function initialize()
    {
        self::loadSDK();
        
        $qbcDsn = self::getDSN();
        
        if (!\QuickBooks_Utilities::initialized($qbcDsn)) {
            // Initialize creates the neccessary database schema
            // for queueing up requests and logging
            \QuickBooks_Utilities::initialize($qbcDsn);
            QBMain::addLog(__FUNCTION__, 'initialize');
            // This creates a username and password
            // which is used by the Web Connector to authenticate
            \QuickBooks_Utilities::createUser(
                $qbcDsn,
                self::getUsername(),
                self::getPassword()
            );
            QBMain::addLog(__FUNCTION__, 'createUser: ' . self::getUsername());
        }
    }
    
    /**
     * Start QB server
     * 
     * @param array $map
     * @param array $errmap
     * 
     * @return void
     */
    public static function server($map, $errmap)
    {
        self::loadSDK();
        
        $qbcDsn = self::getDSN();
        
        // An array of callback hooks
        $hooks = array();

        // Logging level
        // Use this level until you're sure everything works!!!
        $log_level = QUICKBOOKS_LOG_DEVELOP;

        // What SOAP server you're using 
        // A pure-PHP SOAP server (no PHP ext/soap extension required,
        // also makes debugging easier)
        $soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;
        
        // See http://www.php.net/soap
        $soap_options = array(
        );
        
        // See the comments in the QuickBooks/Server/Handlers.php file
        $handler_options = array(
            'deny_concurrent_logins' => false,
            'deny_reallyfast_logins' => false,
        );
        
        // See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file
        // ( i.e. 'Mysql.php', etc. )
        $driver_options = array(
        );

        $callback_options = array(
        );

        // Create a new server and tell it to handle the requests
        $Server = new \QuickBooks_WebConnector_Server(
            $qbcDsn,
            $map,
            $errmap,
            $hooks,
            $log_level,
            $soapserver,
            QUICKBOOKS_WSDL,
            $soap_options,
            $handler_options,
            $driver_options,
            $callback_options
        );

        $response = $Server->handle(true, true);
    }
    
    /**
     * Delete an item from QB queue
     * 
     * @param string $action
     * @param string $id
     * 
     * @return boolean
     */
    public static function qbcDeleteFromQueue($action, $id)
    {
        if (!empty($action) && !empty($id)) {
            
            Database::getEM()->getConnection()->query("
                DELETE FROM quickbooks_queue
                WHERE qb_action = '$action' AND ident = '$id'
            ");
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Filter queue in order to prevent errors in Web Connector
     * like "Error -2: No registered functions for action..."
     * 
     * @param array $allowedActions
     * 
     * @return void
    */
    public static function qbcFilterQueue($allowedActions = array()) {

        $actions = array_keys($allowedActions);

        if (!empty($actions)) {
            $connection = Database::getEM()->getConnection();

            $connection->query("
                DELETE FROM quickbooks_queue
                WHERE qb_action NOT IN ('" . implode("','", $actions) . "')
                    OR qb_status != 'q'
            ");
        }
    }
    
    /**
     * Checks if item is already in QB queue
     * 
     * @param string $action
     * @param string $id
     * 
     * @return boolean
     */
    public static function qbcAlreadyInQueue($action, $id)
    {
        $count = 0;
        
        if (!empty($action) && !empty($id)) {
            $connection = Database::getEM()->getConnection();

            $count = $connection->query("
                SELECT COUNT(*)
                FROM quickbooks_queue
                WHERE qb_action = '$action' AND ident = '$id'
            ")->fetchOne();
        }
        
        return ($count > 0);
    }
    
    /**
     * Add new record to connector queue
     */
    public static function qbcQueueRequest($action, $id)
    {
        if (self::qbcAlreadyInQueue($action, $id)) {
            return false;
        } else {
            self::loadSDK();
            $qbcDsn = self::getDSN();
            $Queue = new \QuickBooks_WebConnector_Queue($qbcDsn);
            
            return $Queue->enqueue($action, $id);
        }
    }
    
    /**
     * Escape values in XML requests
     * 
     * @param string $str
     * 
     * @return mixed
     */
    public static function qbcEscapeValue($str)
    {
        if (is_array($str)) {
        
            return $str;

        } else {

            $str = strtr(
                $str,
                [
                    '’' => '&#39;', '”' => '&#34;', '™' => '&#8482;',
                    '&' => '&#38;', '<' => '&#60;', '>' => '&#62;',
                    '–' => '&#8211;', '—' => '&#8212;', '‘' => '&#8216;',
                    '“' => '&#8220;', '„' => '&#8222;', '"' => '&#34;',
                    '©' => '&#169;', '®' => '&#174;', "'" => '&#39;',
                    'À' => '&#192;', 'Á' => '&#193;', 'Â' => '&#194;',
                    'Ã' => '&#195;', 'Ä' => '&#196;', 'Å' => '&#197;',
                    'Æ' => '&#198;', 'Ç' => '&#199;', 'È' => '&#200;',
                    'É' => '&#201;', 'Ê' => '&#202;', 'Ë' => '&#203;',
                    'Ì' => '&#204;', 'Í' => '&#205;', 'Î' => '&#206;',
                    'Ï' => '&#207;', 'Ð' => '&#208;', 'Ñ' => '&#209;',
                    'Ò' => '&#210;', 'Ó' => '&#211;', 'Ô' => '&#212;',
                    'Õ' => '&#213;', 'Ö' => '&#214;', 'Ø' => '&#216;',
                    'Ù' => '&#217;', 'Ú' => '&#218;', 'Û' => '&#219;',
                    'Ü' => '&#220;', 'Ý' => '&#221;', 'Þ' => '&#222;',
                    'ß' => '&#223;', 'à' => '&#224;', 'á' => '&#225;',
                    'â' => '&#226;', 'ã' => '&#227;', 'ä' => '&#228;',
                    'å' => '&#229;', 'æ' => '&#230;', 'ç' => '&#231;',
                    'è' => '&#232;', 'é' => '&#233;', 'ê' => '&#234;',
                    'ë' => '&#235;', 'ì' => '&#236;', 'í' => '&#237;',
                    'î' => '&#238;', 'ï' => '&#239;', 'ð' => '&#240;',
                    'ñ' => '&#241;', 'ò' => '&#242;', 'ó' => '&#243;',
                    'ô' => '&#244;', 'õ' => '&#245;', 'ö' => '&#246;',
                    'ø' => '&#248;', 'ù' => '&#249;', 'ú' => '&#250;',
                    'û' => '&#251;', 'ü' => '&#252;', 'ý' => '&#253;',
                    'þ' => '&#254;', 'ÿ' => '&#255;', '°' => '&#176;',
                    '²' => '&#178;', '³' => '&#179;', '¹' => '&#185;',
                ]
            );

            $str = iconv('UTF-8', 'ISO-8859-1//IGNORE', $str);

            $str = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/', '', $str);

            $str = rtrim(rtrim($str, '&#'), '&');

            return trim($str);
        }
    }
    
    /**
     * Add/update record of Customers,Products,Orders,Errors entities
     * 
     * @param string $repoName  Repo name
     * @param array  $ids       Ids of entity
     * @param array  $queryData Parameters to add/update
     * 
     * @return boolean
     */
    private static function qbcAddRecord($repoName, $ids, $queryData)
    {
        $repoName = 'Qualiteam\SkinActQuickbooks\Model\\' . $repoName;
        
        if (class_exists($repoName)) {
            
            $repo = Database::getRepo($repoName);
            $table = $repo->getTableName();
            
            if (strpos($repoName, 'QuickbooksProducts') === false) {
                $id = $ids[array_key_first($ids)];
            } else {
                $id = $ids;
            }
            
            if ($repo->recordExists($id)) {
                
                $query = "UPDATE {$table} SET ";
                
                $updateFields = [];
                foreach ($queryData as $fieldName => $fieldValue) {
                    $updateFields[] = "{$fieldName} = :{$fieldName}";
                }
                $query .= implode(', ', $updateFields);
                
                $where = [];
                foreach ($ids as $fieldName => $fieldValue) {
                    $where[] = "{$fieldName} = :{$fieldName}";
                }
                $query .= " WHERE " . implode(" AND ", $where);
                
            } else {
                
                $callback = function ($v) {return ':' . $v;};
                $query = "INSERT IGNORE INTO {$table} "
                    . "(" . implode(', ' , array_keys($queryData)) . ") "
                    . "VALUES"
                    . "(" . implode(', ', array_map($callback, array_keys($queryData))) . ")";
            }
            
            Database::getEM()->getConnection()->executeQuery(
                $query,
                $queryData
            );

            return true;
        }
        
        return false;
    }
    
    /**
     * Generate a qbXML request to add a particular customer to QuickBooks
     */
    public static function qbcCustomerAddRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        // Find profile by ID
        $profile = Database::getRepo('XLite\Model\Profile')->find($ID);
        $billAddr = $profile->getBillingAddress();
        $shipAddr = $profile->getShippingAddress();
        $contact = $profile->getName();
        list($firstname, $lastname) = explode(' ', $contact);
        
        $company = ($billAddr->getFieldValue('company'))
                 ? $billAddr->getFieldValue('company')->getValue()
                 : '';
        
        $arr = [];
        $arr['login'] = substr($profile->getLogin(), 0, 41);
        $arr['company'] = substr($company, 0, 41);
        $arr['firstname'] = substr($firstname, 0, 25);
        $arr['lastname'] = substr($lastname, 0, 25);
        $arr['email'] = substr($profile->getLogin(), 0, 1023);

        $contact = self::qbcEscapeValue(substr($profile->getName(), 0, 41));

        $objMap = new QuickbooksConnector();
        $arr = array_map([$objMap, 'qbcEscapeValue'], $arr);
        
        // Billing address
        
        $bAddr = [];
        $bAddr['country'] = substr($billAddr->getCountry()->getCode(), 0, 31);
        $bAddr['state']   = substr($billAddr->getState()->getCode(), 0, 21);
        $bAddr['city']    = substr($billAddr->getCity(), 0, 31);
        $bAddr['zipcode'] = substr($billAddr->getZipcode(), 0, 13);

        if ($billAddr->getFieldValue('phone')) {
            $bAddr['phone'] = $billAddr->getFieldValue('phone')->getValue();
            $bAddr['phone'] = substr(
                preg_replace('/[^0-9]/', '', $bAddr['phone']),
                0, 21
            );
        }
        
        if ($billAddr->getFieldValue('fax')) {
            $bAddr['fax'] = substr(
                $billAddr->getFieldValue('fax')->getValue(),
                0, 21
            );
        }
        
        $bAddr['address'] = substr($billAddr->getStreet(), 0, 41);
        if ($billAddr->getFieldValue('street_2')) {
            $bAddr['address_2'] = substr(
                $billAddr->getFieldValue('street_2')->getValue(),
                0, 41
            );
        }
        
        $bAddr = array_map([$objMap, 'qbcEscapeValue'], $bAddr);
        
        // Shipping address
        
        $sAddr = [];
        $sAddr['country'] = substr($shipAddr->getCountry()->getCode(), 0, 31);
        $sAddr['state']   = substr($shipAddr->getState()->getCode(), 0, 21);
        $sAddr['city']    = substr($shipAddr->getCity(), 0, 31);
        $sAddr['zipcode'] = substr($shipAddr->getZipcode(), 0, 13);

        if ($shipAddr->getFieldValue('phone')) {
            $sAddr['phone'] = $shipAddr->getFieldValue('phone')->getValue();
            $sAddr['phone'] = substr(
                preg_replace('/[^0-9]/', '', $sAddr['phone']),
                0, 21
            );
        }
        
        if ($shipAddr->getFieldValue('fax')) {
            $sAddr['fax'] = substr(
                $shipAddr->getFieldValue('fax')->getValue(),
                0, 21
            );
        }
        
        $sAddr['address'] = substr($shipAddr->getStreet(), 0, 41);
        if ($shipAddr->getFieldValue('street_2')) {
            $sAddr['address_2'] = substr(
                $shipAddr->getFieldValue('street_2')->getValue(),
                0, 41
            );
        }
        
        $sAddr = array_map([$objMap, 'qbcEscapeValue'], $sAddr);
        
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="2.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<CustomerAddRq requestID="{$requestID}">
<CustomerAdd>
<Name>{$arr['login']}</Name>
<CompanyName>{$arr['company']}</CompanyName>
<FirstName>{$arr['firstname']}</FirstName>
<LastName>{$arr['lastname']}</LastName>
<BillAddress>
<Addr1>{$bAddr['address']}</Addr1>
<Addr2>{$bAddr['address_2']}</Addr2>
<City>{$bAddr['city']}</City>
<State>{$bAddr['state']}</State>
<PostalCode>{$bAddr['zipcode']}</PostalCode>
<Country>{$bAddr['country']}</Country>
</BillAddress>
<ShipAddress>
<Addr1>{$sAddr['address']}</Addr1>
<Addr2>{$sAddr['address_2']}</Addr2>
<City>{$sAddr['city']}</City>
<State>{$sAddr['state']}</State>
<PostalCode>{$sAddr['zipcode']}</PostalCode>
<Country>{$sAddr['country']}</Country>
</ShipAddress>
<Phone>{$bAddr['phone']}</Phone>
<AltPhone>{$sAddr['phone']}</AltPhone>
<Fax>{$bAddr['fax']}</Fax>
<Email>{$arr['email']}</Email>
<Contact>{$contact}</Contact>
</CustomerAdd>
</CustomerAddRq>
</QBXMLMsgsRq>
</QBXML>
XML;
        
        QBMain::addLog(
            QBMain::QBC_CUSTOMERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcCustomerAddResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_CUSTOMERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        if (strpos($xml, 'element is already in use') !== false) {
            
            self::qbcQueueRequest(QUICKBOOKS_QUERY_CUSTOMER, $ID);
            self::qbcDeleteFromQueue($action, $ID);

            return true;
        }

        $queryData = array(
            'profile_id'              => $ID,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_listid'       => $idents['ListID'],
        );

        // Reset orders errors of this customer (if exist)
        if (!empty($idents['ListID'])) {
            $errorOrders = Database::getRepo(QuickbooksOrderErrors::class)
                ->getOrderIdsByProfile($ID);
            if (!empty($errorOrders)) {
                Database::getRepo(QuickbooksOrderErrors::class)
                    ->deleteOrdersErrors($errorOrders);
            }
        }
        
        self::qbcAddRecord(
            'QuickbooksCustomers',
            ['profile_id' => $ID],
            $queryData
        );

        return true;
    }
    
    /**
     * Generate a qbXML request to query customer data from QuickBooks
     */
    public static function qbcCustomerQueryRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        // Find profile by ID
        $profile = Database::getRepo('XLite\Model\Profile')->find($ID);
        $login = substr($profile->getLogin(), 0, 41);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="2.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<CustomerQueryRq requestID="{$requestID}">
<FullName>{$login}</FullName>
</CustomerQueryRq>
</QBXMLMsgsRq>
</QBXML>
XML;
	
        QBMain::addLog(
            QBMain::QBC_CUSTOMERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcCustomerQueryResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_CUSTOMERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        $queryData = array(
            'profile_id'              => $ID,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_listid'       => $idents['ListID'],
        );

        // Reset orders errors of this customer (if exist)
        if (!empty($idents['ListID'])) {
            $errorOrders = Database::getRepo(QuickbooksOrderErrors::class)
                ->getOrderIdsByProfile($ID);
            if (!empty($errorOrders)) {
                Database::getRepo(QuickbooksOrderErrors::class)
                    ->deleteOrdersErrors($errorOrders);
            }
        }
        
        self::qbcAddRecord(
            'QuickbooksCustomers',
            ['profile_id' => $ID],
            $queryData
        );

        return true;
    }
    
    /**
     * Generate a qbXML request to add inventory item to QuickBooks
     */
    public static function qbcProductAddRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;
        
        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $objMap = new QuickbooksConnector();
        $arr = array_map([$objMap, 'qbcEscapeValue'], $arr);

        $qbcItemMappingName = Database::getRepo(QuickbooksProducts::class)
            ->getQuickbooksFullname($product_id, $variant_id);

        if (empty($qbcItemMappingName)) {
            $qbcItemMappingName = $arr['sku'];
        }

        $qbcItemMappingName = substr(
            self::qbcEscapeValue($qbcItemMappingName),
            0, 31
        );
        
        $incomeAccount = self::productIncomeAccount();
        $cogsAccount   = self::productCogsAccount();
        $assetAccount  = self::productAssetAccount();

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="7.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<ItemInventoryAddRq requestID="{$requestID}" >
<ItemInventoryAdd>
<Name>{$qbcItemMappingName}</Name>
<SalesDesc>{$arr['name']}</SalesDesc>
<SalesPrice>{$arr['price']}</SalesPrice>
<IncomeAccountRef>
<FullName>{$incomeAccount}</FullName>
</IncomeAccountRef>
<COGSAccountRef>
<FullName>{$cogsAccount}</FullName>
</COGSAccountRef>
<AssetAccountRef>
<FullName>{$assetAccount}</FullName>
</AssetAccountRef>
<QuantityOnHand>{$arr['amount']}</QuantityOnHand>
</ItemInventoryAdd>
</ItemInventoryAddRq>
</QBXMLMsgsRq>
</QBXML>
XML;
        
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcProductAddResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        if (strpos($xml, 'element is already in use') !== false) {
            
            self::qbcQueueRequest(QUICKBOOKS_QUERY_INVENTORYITEM, $ID);
            self::qbcDeleteFromQueue($action, $ID);

            return true;
        }

        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;
        
        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $queryData = array(
            'product_id'              => $product_id,
            'variant_id'              => $variant_id,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_listid'       => $idents['ListID'],
        );

        if (preg_match('/<SalesPrice>(.*)<\/SalesPrice>/', $xml, $matches)) {
            $queryData['price'] = floatval($matches[1]);
        } else {
            $queryData['price'] = $arr['price'];
        }
        
        self::qbcAddRecord(
            'QuickbooksProducts',
            ['product_id' => $product_id, 'variant_id' => $variant_id],
            $queryData
        );

        return true;
    }
    
    /**
     * Generate a qbXML request to query inventory data from QuickBooks
     */
    public static function qbcProductQueryRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;

        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $objMap = new QuickbooksConnector();
        $arr = array_map([$objMap, 'qbcEscapeValue'], $arr);

        $qbcItemMappingName = Database::getRepo(QuickbooksProducts::class)
            ->getQuickbooksFullname($product_id, $variant_id);

        if (empty($qbcItemMappingName)) {
            $qbcItemMappingName = $arr['sku'];
        }

        $qbcItemMappingName = substr(
            self::qbcEscapeValue($qbcItemMappingName),
            0, 31
        );

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="2.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<ItemInventoryQueryRq requestID="{$requestID}">
<NameFilter>
<MatchCriterion>Contains</MatchCriterion>
<Name>{$qbcItemMappingName}</Name>
</NameFilter>
</ItemInventoryQueryRq>
</QBXMLMsgsRq>
</QBXML>
XML;
        
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcProductQueryResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;
        
        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $queryData = array(
            'product_id'              => $product_id,
            'variant_id'              => $variant_id,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_listid'       => $idents['ListID'],
        );
        
        if (preg_match('/<SalesPrice>(.*)<\/SalesPrice>/', $xml, $matches)) {
            $queryData['price'] = $matches[1];
        } else {
            $queryData['price'] = $arr['price'];
        }
        
        self::qbcAddRecord(
            'QuickbooksProducts',
            ['product_id' => $product_id, 'variant_id' => $variant_id],
            $queryData
        );
        
        return true;
    }
    
    /**
     * Generate a qbXML request to modify inventory data in QuickBooks
     */
    public static function qbcProductModRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;

        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $objMap = new QuickbooksConnector();
        $arr = array_map([$objMap, 'qbcEscapeValue'], $arr);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="7.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<ItemInventoryModRq requestID="{$requestID}" >
<ItemInventoryMod>
<ListID>{$arr['quickbooks_listid']}</ListID>
<EditSequence>{$arr['quickbooks_editsequence']}</EditSequence>
<SalesPrice>{$arr['price']}</SalesPrice>
<ApplyIncomeAccountRefToExistingTxns>0</ApplyIncomeAccountRefToExistingTxns>
</ItemInventoryMod>
</ItemInventoryModRq>
</QBXMLMsgsRq>
</QBXML>
XML;
        
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcProductModResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_PRODUCTS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );

        $idCheck = explode('_', $ID);
        $product_id = intval($idCheck[0]);
        $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;
        
        $arr = Database::getRepo(QuickbooksProducts::class)
            ->getProductData($product_id, $variant_id);

        $queryData = array(
            'product_id'              => $product_id,
            'variant_id'              => $variant_id,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_listid'       => $idents['ListID'],
        );

        if (preg_match('/<SalesPrice>(.*)<\/SalesPrice>/', $xml, $matches)) {
            $queryData['price'] = $matches[1];
        } else {
            $queryData['price'] = $arr['price'];
        }
        
        self::qbcAddRecord(
            'QuickbooksProducts',
            ['product_id' => $product_id, 'variant_id' => $variant_id],
            $queryData
        );

        return true;
    }
    
    /**
     * Generate a qbXML response to add sales receipt to QuickBooks
     */
    public static function qbcSalesorderAddRequest(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $version, $locale
    ) {
        // Find order by ID
        $order = Database::getRepo('XLite\Model\Order')->find($ID);

        if ($order) {
            $objMap = new QuickbooksConnector();
            $arr = [];
            $arr['orderid'] = $order->getOrderNumber();
            $arr['date_fmt'] = date('Y-m-d', $order->getDate());
            $arr = array_map([$objMap, 'qbcEscapeValue'], $arr);
            $origProfile = $order->getOrigProfile();
            $profile = $order->getProfile();
            if (!$origProfile) $origProfile = $profile;
            
            // Customer
            
            $customerFullname = substr($origProfile->getLogin(), 0, 209);
            $customerRef = "<FullName>{$customerFullname}</FullName>";
            $customerListid = Database::getRepo(QuickbooksCustomers::class)
                ->getQuickbooksListid($origProfile->getProfileId());
            if (!empty($customerListid)) {
                $customerRef = "<ListID>{$customerListid}</ListID>";
            }
            
            // Class
            
            $orderClassRef = self::orderClassRef();
            if (!empty($orderClassRef)) {
                $classRef = <<<XML

<ClassRef>
<FullName>{$orderClassRef}</FullName>
</ClassRef>
XML;
            }
            
            // Template
            
            $orderTemplateRef = self::orderTemplateRef();
            if (!empty($orderTemplateRef)) {
                $templateRef = <<<XML

<TemplateRef>
<FullName>{$orderTemplateRef}</FullName>
</TemplateRef>
XML;
            }
            
            // Billing address

            $billAddr = $profile->getBillingAddress();
            $bAddr = [];
            $bAddr['country'] = substr($billAddr->getCountry()->getCode(), 0, 31);
            $bAddr['state']   = substr($billAddr->getState()->getCode(), 0, 21);
            $bAddr['city']    = substr($billAddr->getCity(), 0, 31);
            $bAddr['zipcode'] = substr($billAddr->getZipcode(), 0, 13);

            if ($billAddr->getFieldValue('phone')) {
                $bAddr['phone'] = $billAddr->getFieldValue('phone')->getValue();
                $bAddr['phone'] = substr(
                    preg_replace('/[^0-9]/', '', $bAddr['phone']),
                    0, 21
                );
            }

            if ($billAddr->getFieldValue('fax')) {
                $bAddr['fax'] = substr(
                    $billAddr->getFieldValue('fax')->getValue(),
                    0, 21
                );
            }

            $bAddr['address'] = substr($billAddr->getStreet(), 0, 41);
            $bAddr['address_2'] = '';
            
            if ($billAddr->getFieldValue('street_2')) {
                $bAddr['address_2'] = substr(
                    $billAddr->getFieldValue('street_2')->getValue(),
                    0, 41
                );
            }

            $bAddr = array_map([$objMap, 'qbcEscapeValue'], $bAddr);
            
            // Shipping address

            $shipAddr = $profile->getShippingAddress();
            $sAddr = [];
            $sAddr['country'] = substr($shipAddr->getCountry()->getCode(), 0, 31);
            $sAddr['state']   = substr($shipAddr->getState()->getCode(), 0, 21);
            $sAddr['city']    = substr($shipAddr->getCity(), 0, 31);
            $sAddr['zipcode'] = substr($shipAddr->getZipcode(), 0, 13);

            if ($shipAddr->getFieldValue('phone')) {
                $sAddr['phone'] = $shipAddr->getFieldValue('phone')->getValue();
                $sAddr['phone'] = substr(
                    preg_replace('/[^0-9]/', '', $sAddr['phone']),
                    0, 21
                );
            }

            if ($shipAddr->getFieldValue('fax')) {
                $sAddr['fax'] = substr(
                    $shipAddr->getFieldValue('fax')->getValue(),
                    0, 21
                );
            }

            $sAddr['address'] = substr($shipAddr->getStreet(), 0, 41);
            $sAddr['address_2'] = '';
            
            if ($shipAddr->getFieldValue('street_2')) {
                $sAddr['address_2'] = substr(
                    $shipAddr->getFieldValue('street_2')->getValue(),
                    0, 41
                );
            }

            $sAddr = array_map([$objMap, 'qbcEscapeValue'], $sAddr);
            
            // Order items
            
            $orderItems = '';
            
            $items = $order->getItems();
            
            if (!empty($items)) {
                
                $xcVariantsModule = class_exists('XC\ProductVariants\Main');
                
                foreach ($items as $item) {
                    
                    if (empty($item->getSku()) || !$item->getProduct()) {
                        
                        continue;
                    }
                    
                    $product = $item->getProduct();
                    
                    $itemSku = self::qbcEscapeValue($item->getSku());
                    $itemRef = "<FullName>{$itemSku}</FullName>";
                    $product_id = $product->getProductId();
                    $variant_id = 0;
                    
                    if ($xcVariantsModule) {
                        $variant = $item->getVariant();
                        if ($variant) {
                            $variant_id = $variant->getId();
                        }
                    }
                    
                    $itemQBData = Database::getRepo(QuickbooksProducts::class)
                        ->getProductData($product_id, $variant_id);
                    
                    if (!empty($itemQBData['quickbooks_fullname'])) {
                        
                        if (strlen($itemQBData['quickbooks_fullname']) > 31) {
                            
                            $itemQBData['quickbooks_fullname'] = substr(
                                $itemQBData['quickbooks_fullname'],
                                0, 
                                31
                            );
                        }
                        
                        $itemQBData['quickbooks_fullname'] = self::qbcEscapeValue(
                            $itemQBData['quickbooks_fullname']
                        );
                        
                        $itemRef = "<FullName>{$itemQBData['quickbooks_fullname']}</FullName>";
                        
                    } elseif (!empty($itemQBData['quickbooks_listid'])) {
                        
                        $itemQBData['quickbooks_listid'] = self::qbcEscapeValue(
                            $itemQBData['quickbooks_listid']
                        );
                        
                        $itemRef = "<ListID>{$itemQBData['quickbooks_listid']}</ListID>";
                    }
                    
                    $taxFullName = 'NON';
                    if ($item->getTaxable()) {
                        $taxFullName = 'TAX';
                    }
                    
                    $itemArr = [];
                    $itemArr['name'] = $itemQBData['name'];
                    $itemArr['price'] = $item->getItemNetPrice();
                    $itemArr['amount'] = $item->getAmount();
                    $itemArr = array_map([$objMap, 'qbcEscapeValue'], $itemArr);
                    
                    $orderItems .= <<<XML

<SalesReceiptLineAdd>
<ItemRef>
{$itemRef}
</ItemRef>
<Desc>{$itemArr['name']}</Desc>
<Quantity>{$itemArr['amount']}</Quantity>
<Rate>{$itemArr['price']}</Rate>
<SalesTaxCodeRef>
<FullName>{$taxFullName}</FullName>
</SalesTaxCodeRef>
</SalesReceiptLineAdd>
XML;
                }
            }
        }

        // Assign tax by name
        
        $order_tax_full_name = $orderSalesTaxRef = '';
        $shippingTaxesApplied = false;
        $taxCost = $order->getSurchargesSubtotal(Surcharge::TYPE_TAX);

        if (!empty($taxCost)) {
            
            $salesTaxModel = 'CDev\SalesTax\Model\Tax';
            $cdevSalesTaxModule = class_exists($salesTaxModel);
            $taxSurcharges = $order->getSurchargesByType(Surcharge::TYPE_TAX);
            
            foreach ($taxSurcharges as $taxSurcharge) {
                
                $sTaxCost = $order->getCurrency()->roundValue(
                    $taxSurcharge->getValue()
                );
                
                if ($taxCost == $sTaxCost) {
                    $sClass = $taxSurcharge->getClass();
                    
                    // Sales Tax
                    
                    if (
                        strpos($sClass, 'CDev\SalesTax') !== false
                        && $cdevSalesTaxModule
                    ) {
                        
                        $tax = Database::getRepo($salesTaxModel)->getTax();
                        
                        if ($tax) {
                            $order_tax_full_name = $tax->getQbTaxName();
                            $zones = [];
                            if ($shipAddr) {
                                $findZones = Database::getRepo('XLite\Model\Zone')
                                    ->findApplicableZones($shipAddr->toArray());
                                if ($findZones)
                                    foreach ($findZones as $zone)
                                        $zones[] = $zone->getZoneId();
                            }
                            $membership = $profile->getMembership();
                            $shippingRates = $tax->getFilteredShippingRates(
                                $zones, $membership
                            );
                            if (count($shippingRates) > 0) {
                                $shippingTaxesApplied = true;
                            }
                        }
                        
                        break;
                    }
                }
            }
            
            if (!empty($order_tax_full_name)) {
                $orderSalesTaxRef = <<<XML

<ItemSalesTaxRef>
<FullName>{$order_tax_full_name}</FullName>
</ItemSalesTaxRef>
XML;
            }
        }

        // Discount
        
        $orderDiscountRef = self::orderDiscountRef();
        $discountCost = $order->getSurchargesSubtotal(Surcharge::TYPE_DISCOUNT);
        
        if (!empty($orderDiscountRef) && !empty($discountCost)) {
            
            $discount_items = [];
            $discountItems = $order->getSurchargesByType(Surcharge::TYPE_DISCOUNT);
            
            foreach ($discountItems as $discountItem) {
                
                if ($discountItem->getAvailable()) {
                    
                    $dCode  = $discountItem->getCode();
                    $dValue = $discountItem->getValue();
                    
                    $discount_items[$dCode] = [
                        'desc' => $discountItem->getSurchargeName(),
                        'rate' => $order->getCurrency()->roundValue($dValue),
                    ];
                    
                    $cdevCouponsModule = class_exists('CDev\Coupons\Model\Coupon');
                    
                    if (
                        'DCOUPON' == $dCode
                        && $cdevCouponsModule
                    ) {
                        
                        $usedCouponsDesc = [];
                        $usedCoupons = $order->getUsedCoupons();
                        
                        if ($usedCoupons) {
                            
                            foreach ($usedCoupons as $usedCoupon) {
                                $usedCouponsDesc[] = $usedCoupon->getPublicName();
                            }
                            
                            $discount_items[$dCode]['desc']
                                .= ': ' . implode(', ', $usedCouponsDesc);
                            
                        }
                        
                    }
                    
                    $discount_items[$dCode]['desc'] = self::qbcEscapeValue(
                        substr($discount_items[$dCode]['desc'], 0, 4095)
                    );
                    
                }
                
            }
            
            if (!empty($discount_items)) {
                foreach ($discount_items as $ditem) {
                    $orderItems .= <<<XML

<SalesReceiptLineAdd>
<ItemRef>
<FullName>{$orderDiscountRef}</FullName>
</ItemRef>
<Desc>{$ditem['desc']}</Desc>
<Rate>{$ditem['rate']}</Rate>
<SalesTaxCodeRef>
<FullName>NON</FullName>
</SalesTaxCodeRef>
</SalesReceiptLineAdd>
XML;
                }
            }
        }

        // Shipping cost
        
        $orderShippingRef = self::orderShippingRef();
        $shippingCost = $order->getSurchargesSubtotal(Surcharge::TYPE_SHIPPING);
        
        if (
            !empty($orderShippingRef)
            && !empty($shippingCost)
        ) {
            $shippingName = self::qbcEscapeValue($order->getShippingMethodName());
            $shTaxFullName = 'NON';
            if (!empty($shippingTaxesApplied)) {
                $shTaxFullName = 'TAX';
            }
            
            $orderItems .= <<<XML

<SalesReceiptLineAdd>
<ItemRef>
<FullName>{$orderShippingRef}</FullName>
</ItemRef>
<Desc>{$shippingName}</Desc>
<Quantity>1</Quantity>
<Rate>{$shippingCost}</Rate>
<SalesTaxCodeRef>
<FullName>{$shTaxFullName}</FullName>
</SalesTaxCodeRef>
</SalesReceiptLineAdd>
XML;
        }
        
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="7.0"?>
<QBXML>
<QBXMLMsgsRq onError="continueOnError">
<SalesReceiptAddRq requestID="{$requestID}">
<SalesReceiptAdd>
<CustomerRef>
{$customerRef}
</CustomerRef>{$classRef}{$templateRef}
<TxnDate>{$arr['date_fmt']}</TxnDate>
<RefNumber>{$arr['orderid']}</RefNumber>
<BillAddress>
<Addr1>{$bAddr['address']}</Addr1>
<Addr2>{$bAddr['address_2']}</Addr2>
<City>{$bAddr['city']}</City>
<State>{$bAddr['state']}</State>
<PostalCode>{$bAddr['zipcode']}</PostalCode>
<Country>{$bAddr['country']}</Country>
</BillAddress>
<ShipAddress>
<Addr1>{$sAddr['address']}</Addr1>
<Addr2>{$sAddr['address_2']}</Addr2>
<City>{$sAddr['city']}</City>
<State>{$sAddr['state']}</State>
<PostalCode>{$sAddr['zipcode']}</PostalCode>
<Country>{$sAddr['country']}</Country>
</ShipAddress>
<IsPending>false</IsPending>{$orderSalesTaxRef}
<IsToBePrinted>true</IsToBePrinted>
<IsToBeEmailed>false</IsToBeEmailed>{$orderItems}
</SalesReceiptAdd>
</SalesReceiptAddRq>
</QBXMLMsgsRq>
</QBXML>
XML;
        
        QBMain::addLog(
            QBMain::QBC_ORDERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );
        
        return $xml;
    }
    
    /**
     * Receive a response from QuickBooks
     */
    public static function qbcSalesorderAddResponse(
        $requestID, $user, $action, $ID, $extra, &$err,
        $last_action_time, $last_actionident_time, $xml, $idents
    ) {
        QBMain::addLog(
            QBMain::QBC_ORDERS_LOG,
            __FUNCTION__,
            func_get_args(),
            $xml
        );

        $queryData = array(
            'order_id'                => $ID,
            'quickbooks_editsequence' => $idents['EditSequence'],
            'quickbooks_txnid'        => $idents['TxnID'],
        );
        
        self::qbcAddRecord(
            'QuickbooksOrders',
            ['order_id' => $ID],
            $queryData
        );
        
        return true;
    }
    
    /**
     * Catch and handle an error from QuickBooks
     */
    public static function qbcErrorCatchAll(
        $requestID, $user, $action, $ID, $extra, &$err,
        $xml, $errnum, $errmsg
    ) {
        
        $message  = 'Request ID: ' . $requestID . "\r\n";
        $message .= 'User: ' . $user . "\r\n";
        $message .= 'Action: ' . $action . "\r\n";
        $message .= 'ID: ' . $ID . "\r\n";
        $message .= 'Extra: ' . print_r($extra, true) . "\r\n";
        $message .= 'Error number: ' . $errnum . "\r\n";
        $message .= 'Error message: ' . $errmsg . "\r\n";
        $message .= "XML:\n" . $xml;

        switch($action) {
            
            case QUICKBOOKS_ADD_CUSTOMER:
            case QUICKBOOKS_QUERY_CUSTOMER:
                
                $label = QBMain::QBC_CUSTOMERS_LOG;
                
                break;
            
            case QUICKBOOKS_ADD_INVENTORYITEM:
            case QUICKBOOKS_QUERY_INVENTORYITEM:
            case QUICKBOOKS_MOD_INVENTORYITEM:
                
                $label = QBMain::QBC_PRODUCTS_LOG;
                
                $idCheck = explode('_', $ID);
                $product_id = intval($idCheck[0]);
                $variant_id = (!empty($idCheck[1])) ? intval($idCheck[1]) : 0;
                
                if (
                    /* The given object ID in the field "list ID" is invalid */
                    intval($errnum) == 3000
                    /* Object specified in the request cannot be found */
                    || intval($errnum) == 3120
                    /* There is a missing element: "ListID" */
                    || intval($errnum) == 3150
                    /* The provided edit sequence is out-of-date */
                    || intval($errnum) == 3200
                ) {
                    
                    Database::getRepo(QuickbooksProducts::class)
                        ->deleteVariant($product_id, $variant_id);
                    self::qbcQueueRequest(QUICKBOOKS_ADD_INVENTORYITEM, $ID);
                }
                
                break;
                
            case QUICKBOOKS_ADD_SALESORDER:
                
                $label = QBMain::QBC_ORDERS_LOG;
                
                $skipOrderError = false;
                
                if (
                    intval($errnum) == 3140
                    && strpos($errmsg, 'QuickBooks Customer') !== false
                ) {
                    
                    $profileId = Database::getRepo(QuickbooksOrders::class)
                        ->getProfileIdFromOrder($ID);
                    
                    $userSynced = $userExists = false;
                    
                    if ($profileId) {
                        
                        $userSynced = Database::getRepo(QuickbooksCustomers::class)
                            ->checkCustomerSynced($profileId);
                        
                        $userExists = Database::getRepo(QuickbooksCustomers::class)
                            ->checkCustomerExists($profileId);
                    }
                    
                    if (!$userSynced && $userExists) {
                        
                        $skipOrderError = true;
                        
                    } else {
                        
                        $errmsg .= ' (X-Cart customer deleted)';
                        
                        // Set "Do not import this order to QuickBooks" checkbox
                        
                        $order = Database::getRepo('XLite\Model\Order')
                            ->find($ID);
                        
                        if ($order) {
                            
                            $order->setQbcIgnore('Y');
                            
                            Database::getEM()->flush();
                        }
                    }
                    
                } elseif (intval($errnum) == 3240) { // Customer needs to be re-synced
                    
                    preg_match('/\"(.+)\"/Uis', $errmsg, $errListID);
                    
                    if (!empty($errListID)) {
                        
                        $profileId = Database::getRepo(QuickbooksCustomers::class)
                            ->getProfileByListid($errListID[1]);
                        
                        if ($profileId) {
                            
                            Database::getRepo(QuickbooksCustomers)
                                ->deleteCustomers($profileId);
                            
                            $skipOrderError = true;
                        }
                    }
                }
                
                if (!$skipOrderError) {
                    
                    // Add order error
                    
                    $queryData = array(
                        'order_id' => $ID,
                        'errors'   => "Error $errnum: $errmsg",
                    );
                    
                    self::qbcAddRecord(
                        'QuickbooksOrderErrors',
                        ['order_id' => $ID],
                        $queryData
                    );
                    
                }
                
                break;
                
            default:
                
                $label = QBMain::QBC_ERRORS_LOG;
        }

        QBMain::addLog(
            $label,
            'ERROR',
            $message
        );

        return true;
    }
}