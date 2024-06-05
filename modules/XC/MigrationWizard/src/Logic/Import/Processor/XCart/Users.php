<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Users processor
 */
class Users extends \XLite\Logic\Import\Processor\Customers
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const LOGIN_TYPE_CUSTOMER = 'C';
    public const LOGIN_TYPE_PROVIDER = 'P';
    public const LOGIN_TYPE_ADMIN    = 'A';

    public const ACCESS_LEVEL_ADMIN    = 100;
    public const ACCESS_LEVEL_CUSTOMER = 0;

    public const FIELD_SERVICE_NAME_LENGHT = 128;

    public const ADDRESS_FIELD_VALUE_LENGHT = 254; // 255-1 is required,proved by tests

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    private static $addressFieldsLinks = [
        'city'      => 'city',
        'country'   => 'country_code',
        'firstname' => 'firstname',
        'lastname'  => 'lastname',
        'phone'     => 'phone',
        'state'     => 'state',
        'address'   => 'street',
        'title'     => 'title',
        'zipcode'   => 'zipcode',
    ];

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Reviews',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Votes',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\XC4LoginVirtual',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if (!empty($columns['password'][static::COLUMN_LENGTH])) {
            unset($columns['password'][static::COLUMN_LENGTH]);
        }

        return array_merge(
            $columns,
            [
                'profile_id'              => [],
                'anonymous'               => [
                    static::COLUMN_IS_KEY => true,
                ],
                'access_level'            => [
                    static::COLUMN_IS_KEY => true,
                ],
                'pendingMembership'       => [],
                'dateOfLoginAttempt'      => [],
                'countOfLoginAttempts'    => [],
                'additionalAddressFields' => [
                    static::COLUMN_IS_MULTIPLE => true,
                ],

                'xc4EntityId' => [],
            ]
        );
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $customerAccessLevel = self::ACCESS_LEVEL_CUSTOMER;
        $adminAccessLevel    = self::ACCESS_LEVEL_ADMIN;
        $accessLevel         = "IF(c.usertype = 'P' OR c.usertype = 'A', {$adminAccessLevel}, {$customerAccessLevel})";

        $rootAccessCode = \XLite\Model\Role\Permission::ROOT_ACCESS;
        $roles          = "IF(c.usertype = 'P' OR c.usertype = 'A', '{$rootAccessCode}', '')";

        $anonymousCustomer = static::isTableColumnExists('customers', 'is_anonymous_customer')
            ? 'IF(is_anonymous_customer > 0, true, false)'
            : 'false';

        if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
            $idRelatedFieldset = 'c.login AS `xc4EntityId`,'
                . 'c.login AS `profile_id`,'
                . 'c.login AS `billingAddress`,'
                . 'c.login AS `shippingAddress`,'
                . 'c.login AS `additionalAddressFields`,'
                . 'c.login AS `addressField`,';
        } else {
            $idRelatedFieldset = 'c.id AS `xc4EntityId`,'
                . 'c.id AS `profile_id`,'
                . 'c.id AS `billingAddress`,'
                . 'c.id AS `shippingAddress`,'
                . 'c.id AS `additionalAddressFields`,'
                . 'c.id AS `addressField`,';
        }

        $invalidLoginAttempts = 'IF(c.invalid_login_attempts > 0, c.last_login, 0) AS `dateOfLoginAttempt`,'
            . 'c.invalid_login_attempts AS `countOfLoginAttempts`,';
        if (!static::isTableColumnExists('customers', 'invalid_login_attempts')) {
            $invalidLoginAttempts = '0 AS `dateOfLoginAttempt`,'
                . '0 AS `countOfLoginAttempts`,';
        }

        $safe_max_unixtime_limit = '2147000000'; // 2038-01-13 16:53:20

        return $idRelatedFieldset
            . 'c.email AS `login`,'
            . "{$anonymousCustomer} AS `anonymous`,"
            . "{$accessLevel} AS `access_level`,"
            . 'c.password AS `password`,'
            . "FROM_UNIXTIME(LEAST(c.first_login, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) AS `added`,"
            . "FROM_UNIXTIME(LEAST(c.first_login, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) AS `firstLogin`,"
            . "FROM_UNIXTIME(LEAST(c.last_login, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) AS `lastLogin`,"
            . 'IF(c.status = "Y" AND c.usertype = "C", "E", "D") AS `status`,'
            . 'c.referer AS `referer`,'
            . 'c.language AS `language`,'
            . 'm1.membership AS `membership`,'
            . 'm2.membership AS `pendingMembership`,'
            . $invalidLoginAttempts
            . "{$roles} AS `roles`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        return "{$prefix}customers AS c"
            . " LEFT JOIN {$prefix}memberships AS m1"
            . ' ON m1.`membershipid` = c.`membershipid`'
            . " LEFT JOIN {$prefix}memberships AS m2"
            . ' ON m2.`membershipid` = c.`pending_membershipid`';
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = "c.`usertype` IN ('C', 'P', 'A') AND c.`email` <> ''";

        if (static::isDemoModeMigration()) {
            $userIds = static::getDemoUserIds();
            if (!empty($userIds)) {
                $userIds = implode(',', $userIds);
                if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
                    $result .= " AND c.login IN ({$userIds})";
                } else {
                    $result .= " AND c.id IN ({$userIds})";
                }
            }
        }

        return $result;
    }

    /**
     * Define filter SQL
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
            return ['FIELD(c.usertype, "A", "P", "C")', 'c.login'];
        } else {
            return ['FIELD(c.usertype, "A", "P", "C")', 'c.id'];
        }
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            static::REGISTRY_SOURCE => 'profile_id',
            static::REGISTRY_RESULT => 'profile_id',
        ];
    }

    /**
     * Define columns which fetched as id of main entity
     * This ones should be normalized before calculate checksum
     *
     * @return array
     */
    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'addressField',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Users migrated');
    }

    /**
     * Get additional address fields data
     *
     * @param integer $userId
     * @param integer $addressId
     *
     * @return array
     */
    protected function getAdditionalAddressFieldsData($userId, $addressId)
    {
        if (!$userId) {
            return [];
        }

        $address = [];

        $additionalAddressFieldsPDOStatement = $this->getAdditionalAddressFieldsPDOStatement();
        if (
            $additionalAddressFieldsPDOStatement
            && $additionalAddressFieldsPDOStatement->execute([$userId, $addressId])
            && $records = $additionalAddressFieldsPDOStatement->fetchAll(\PDO::FETCH_ASSOC)
        ) {
            foreach ($records as $record) {
                $service_name = preg_replace('/\W/', '_', strtolower($record['field_name']));
                $service_name = substr($service_name, 0, static::FIELD_SERVICE_NAME_LENGHT);
                if (!$this->getAddressField($service_name)) {
                    $addressField = $this->createAddressField($service_name);

                    $this->updateAddressField(
                        $addressField,
                        [
                            'name'         => $record['field_name'],
                            'service_name' => $service_name,
                            'additional'   => true,
                            'required'     => $record['required'],
                        ]
                    );
                }

                $address[$service_name] = $record['value'];
            }
        }

        return $address;
    }

    /**
     * @return \PDOStatement
     */
    protected function getAdditionalAddressFieldsPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT address_book.id                            id,'
            . 'register_fields.field                           field_name,'
            . "IF(register_fields.required <> '', true, false) required,"
            . 'register_field_address_values.value             value'
            . " FROM {$prefix}register_field_address_values register_field_address_values"
            . " INNER JOIN {$prefix}register_fields register_fields"
            . ' ON register_field_address_values.fieldid = register_fields.fieldid'
            . " AND register_fields.section = 'B'"
            . " INNER JOIN {$prefix}address_book address_book"
            . ' ON address_book.id = register_field_address_values.addressid'
            . ' AND address_book.userid = ?'
            . ' AND address_book.id = ?'
            . ' ORDER BY address_book.id'
        );
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating users');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'pending membership' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizePendingMembershipValue($value)
    {
        return $this->normalizeValueAsMembership($value);
    }

    /**
     * Normalize 'date of login attempt' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeDateOfLoginAttemptValue($value)
    {
        return (int) $value;
    }

    /**
     * Normalize 'count of login attempts' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeCountOfLoginAttemptsValue($value)
    {
        return (int) $value;
    }

    /**
     * Normalize 'order' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeOrderValue($value)
    {
        return $value === 'NULL' ? null : $value;
    }

    /**
     * Normalize value as state
     *
     * @param mixed $value       Value
     * @param mixed @countryCode Country code
     *
     * @return string|\XLite\Model\State
     */
    protected function normalizeValueAsStateWithCountry($value, $countryCode)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndState($countryCode, $value)
            ?: $this->normalizeValueAsState($value);
    }

    /**
     * Collect addresses
     *
     * @param $value
     *
     * @return array
     */
    protected function normalizeAddressFieldValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $first  = reset($value);
            $userId = array_pop($first);

            $addresses = [];

            if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
                $addressBookPDOStatement = $this->getOldAddressDataPDOStatement();
                if (
                    $userId
                    && $addressBookPDOStatement
                    && $addressBookPDOStatement->execute([$userId])
                    && $record = $addressBookPDOStatement->fetch(\PDO::FETCH_ASSOC)
                ) {
                    $addressBookFields = ['title', 'firstname', 'lastname', 'address', 'city', 'county', 'state', 'country', 'zipcode'];

                    $equal    = true;
                    $empty_s  = true;
                    $empty_b  = true;
                    $s_fields = ['is_shipping' => true];
                    $b_fields = ['is_billing' => true];
                    foreach ($addressBookFields as $field) {
                        if ($record['b_' . $field] != $record['s_' . $field]) {
                            $equal = false;
                        }

                        if (!empty($record['s_' . $field])) {
                            $empty_s = false;
                        }

                        if (!empty($record['b_' . $field])) {
                            $empty_b = false;
                        }

                        if (array_key_exists($field, static::$addressFieldsLinks)) {
                            $s_fields[static::$addressFieldsLinks[$field]] = $record['s_' . $field];
                            $b_fields[static::$addressFieldsLinks[$field]] = $record['b_' . $field];
                        }
                    }

                    if (!$empty_s && !$equal) {
                        if (isset($s_fields['street'])) {
                            $streetLines        = preg_split('/[\n\r]/', $s_fields['street']);
                            $streetLines        = array_filter($streetLines, static function ($value) {
                                return $value !== '';
                            });
                            $s_fields['street'] = implode(', ', $streetLines);
                        }
                        $s_fields    = $this->substrStrings($s_fields);
                        $addresses[] = $s_fields;
                    }

                    if (!$empty_b && !$equal) {
                        if (isset($b_fields['street'])) {
                            $streetLines        = preg_split('/[\n\r]/', $b_fields['street']);
                            $streetLines        = array_filter($streetLines, static function ($value) {
                                return $value !== '';
                            });
                            $b_fields['street'] = implode(', ', $streetLines);
                        }
                        $b_fields    = $this->substrStrings($b_fields);
                        $addresses[] = $b_fields;
                    }

                    if ($equal && !($empty_b && $empty_s)) {
                        $b_fields['is_shipping'] = true;
                        $addresses[]             = $b_fields;
                    }
                }
            } else {
                $addressBookPDOStatement = $this->getAddressBookPDOStatement();
                if (
                    $userId
                    && $addressBookPDOStatement
                    && $addressBookPDOStatement->execute([$userId])
                    && $records = $addressBookPDOStatement->fetchAll(\PDO::FETCH_ASSOC)
                ) {
                    foreach ($records as $record) {
                        $address = [];
                        foreach ($record as $k => $field) {
                            if (array_key_exists($k, static::$addressFieldsLinks)) {
                                $address[static::$addressFieldsLinks[$k]] = $field;
                            }
                        }

                        if (isset($address['street'])) {
                            $streetLines       = preg_split('/[\n\r]/', $address['street']);
                            $streetLines       = array_filter($streetLines, static function ($value) {
                                return $value !== '';
                            });
                            $address['street'] = implode(', ', $streetLines);
                        }

                        if ($address) {
                            $address['is_shipping'] = $record['default_s'] === 'Y';
                            $address['is_billing']  = $record['default_b'] === 'Y';

                            $address += $this->getAdditionalAddressFieldsData($userId, $record['id']);
                            $address = $this->substrStrings($address);

                            $addresses[] = $address;
                        }
                    }
                }
            }

            return $addresses;
        }, ['normalizeAddressFieldColumn', $value]);
    }

    /**
     * Normalize 'role' value
     *
     * @param string $value Role code
     *
     * @return \XLite\Model\Role
     */
    protected function normalizeRoleValue($value)
    {
        $result = null;

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneByPermissionCode($value);
        }

        return $result;
    }

    /**
     * Normalize 'language' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeLanguageValue($value)
    {
        $value = strtolower($value);
        if ($value == 'us') {
            $value = 'en';
        }

        return parent::normalizeLanguageValue($value);
    }

    // }}} </editor-fold>

    // {{{ Address fields <editor-fold desc="Address fields" defaultstate="collapsed">

    /**
     * @var bool[]
     */
    protected $addressFields = [];

    /**
     * @param string $serviceName
     *
     * @return bool
     */
    protected function getAddressField($serviceName)
    {
        if (!isset($this->addressFields[$serviceName])) {
            $repo = \XLite\Core\Database::getRepo('XLite\Model\AddressField');

            $this->addressFields[$serviceName] = (bool) $repo->findOneByServiceName($serviceName);
        }

        return $this->addressFields[$serviceName];
    }

    /**
     * Insert address field
     *
     * @param string $serviceName
     *
     * @return \XLite\Model\AddressField
     */
    protected function createAddressField($serviceName)
    {
        /** @var \XLite\Model\AddressField $field */
        $field = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->insert(null, false);

        $this->addressFields[$serviceName] = true;

        return $field;
    }

    /**
     * Update address field
     *
     * @param \XLite\Model\AddressField $addressField Address field to update
     * @param array                     $data         New values for address field
     *
     * @return void
     */
    protected function updateAddressField(\XLite\Model\AddressField $addressField, array $data)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Address')->update($addressField, $data, true);
    }

    /**
     * Currently xc_address_field_value supports up to 255 symbols
     *
     * @param array $data New values for address field
     *
     * @return array
     */
    protected function substrStrings(array $data)
    {
        return array_map(
            static function ($field_value) {
                return is_string($field_value) ? substr($field_value, 0, static::ADDRESS_FIELD_VALUE_LENGHT) : $field_value;
            },
            $data
        );
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import password
     *
     * @param \XLite\Model\Profile $model Profile
     * @param string               $value Value
     * @param integer              $index Index
     *
     * @return void
     */
    protected function importPasswordColumn(\XLite\Model\Profile $model, $crypted_password, $index)
    {
        $plain_password = Configuration::textDecrypt($crypted_password) ?: $crypted_password;
        if (
            empty($plain_password)
            || strlen($plain_password) < 2
        ) {
            $plain_password = random_bytes(16); // Only for PHP7
        }

        if (Hash\PasswordHash::isPasswordHash($plain_password)) {
            $crypted = Configuration::textCrypt($plain_password);

            if (
                empty($crypted)
                || strlen($crypted) <= 2
            ) {
                $crypted = Configuration::textCrypt(random_bytes(16));// Only for PHP7
            }

            $model->setPassword($crypted);
        } else {
            parent::importPasswordColumn($model, $plain_password, $index);
        }
    }

    /**
     * Import 'address field' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAddressFieldColumn(\XLite\Model\Profile $model, array $value, array $column)
    {
        $addresses = $this->normalizeAddressFieldValue($value);

        foreach ($model->getAddresses() as $address) {
            \XLite\Core\Database::getRepo('XLite\Model\Address')->delete($address, false);
        }
        $model->getAddresses()->clear();

        $i = 0;
        foreach ($addresses as $address) {
            $this->importAddress($model, $address, $i);
            $i++;
        }
    }

    /**
     * @return \PDOStatement
     */
    protected function getAddressBookPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT address_book.country, address_book.*'
            . " FROM {$prefix}address_book address_book"
            . ' WHERE address_book.userid = ?'
            . ' ORDER BY address_book.id'
        );
    }

    /**
     * @return \PDOStatement
     */
    protected function getOldAddressDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT c.*'
            . " FROM {$prefix}customers c"
            . ' WHERE c.login = ?'
        );
    }

    /**
     * Import address
     *
     * @param \XLite\Model\Profile $model   Profile
     * @param array                $address Address
     * @param integer              $index   Index
     *
     * @return void
     */
    protected function importAddress(\XLite\Model\Profile $model, array $address, $index)
    {
        if (isset($address['state']) && isset($address['country_code'])) {
            $address['state'] = $this->normalizeValueAsStateWithCountry($address['state'], $address['country_code']);
        }

        parent::importAddress($model, $address, $index);
    }

    /**
     * Import roles
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importRolesColumn(\XLite\Model\Profile $model, array $value, array $column)
    {
        $value = array_unique($value);

        parent::importRolesColumn($model, $value, $column);
    }

    /**
     * Import 'profile_id' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importProfileIdColumn($model, $value, array $column)
    {
    }

    // }}} </editor-fold>

    /**
     * Return true if import run in create-only mode
     *
     * @return boolean
     */
    protected function isCreateMode()
    {
        return true;
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $conditions = $this->assembleModelConditions($data);

        unset($conditions['access_level']);
        $model = $conditions ? $this->getRepository()->findOneByImportConditions($conditions) : null;

        $this->isNewModel = !(bool) $model;

        if ($model) {
            $this->currentlyProcessingModel = $model;
        }

        return $model;
    }
}
