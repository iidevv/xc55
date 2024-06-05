<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use XCart\Security\UserRepositoryInterface;
use XLite\Model\Profile as ProfileEntity;

/**
 * The Profile model repository
 */
class Profile extends \XLite\Model\Repo\ARepo implements UserRepositoryInterface
{
    /**
     * Allowable search params
     */
    public const SEARCH_PROFILE_ID     = 'profile_id';
    public const SEARCH_ORDER_ID       = 'order_id';
    public const SEARCH_REFERER        = 'referer';
    public const SEARCH_MEMBERSHIP     = 'membership';
    public const SEARCH_ROLES          = 'roles';
    public const SEARCH_PERMISSIONS    = 'permissions';
    public const SEARCH_LANGUAGE       = 'language';
    public const SEARCH_PATTERN        = 'pattern';
    public const SEARCH_LOGIN          = 'login';
    public const SEARCH_AND_LOGIN      = 'andLogin';
    public const SEARCH_PHONE          = 'phone';
    public const SEARCH_COUNTRY        = 'country';
    public const SEARCH_STATE          = 'state';
    public const SEARCH_CUSTOM_STATE   = 'custom_state';
    public const SEARCH_ADDRESS        = 'address';
    public const SEARCH_USER_TYPE      = 'user_type';
    public const SEARCH_DATE_TYPE      = 'date_type';
    public const SEARCH_DATE_PERIOD    = 'date_period';
    public const SEARCH_START_DATE     = 'startDate';
    public const SEARCH_END_DATE       = 'endDate';
    public const SEARCH_DATE_RANGE     = 'dateRange';
    public const SEARCH_STATUS         = 'status';
    public const SEARCH_ONLY_REAL      = 'onlyReal';

    /**
     * Password length
     */
    public const PASSWORD_LENGTH = 12;

    /**
     * Default Recent administrators list length
     */
    public const DEFAULT_RECENT_ADMINS_LENGTH = 8;

    /**
     * Password characters list
     *
     * @var array
     */
    protected static $passwordChars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z',
    ];

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy, $code);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->linkLeft(sprintf('%s.addresses', $rootAlias));

        return $queryBuilder;
    }

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        return array_merge(
            parent::getExcludedConditions(),
            [
                static::P_ORDER_BY          => [
                    static::SEARCH_MODE_COUNT
                ],
                static::SEARCH_DATE_PERIOD  => static::EXCLUDE_FROM_ANY,
                static::SEARCH_DATE_RANGE   => static::EXCLUDE_FROM_ANY,
            ]
        );
    }

    /**
     * Search profile by login
     *
     * @param string $login User's login
     *
     * @return \XLite\Model\Profile
     */
    public function findByLogin($login)
    {
        return $this->findByLoginPassword($login);
    }

    /**
     * Search profile by login and password
     *
     * @param string  $login    User's login
     * @param string  $password User's password OPTIONAL
     * @param integer $orderId  Order ID related to the profile OPTIONAL
     *
     * @return \XLite\Model\Profile
     */
    public function findByLoginPassword($login, $password = null, $orderId = 0)
    {
        return $this->defineFindByLoginPasswordQuery($login, $password, $orderId)->getSingleResult();
    }

    /**
     * @param integer $profileId
     *
     * @return string
     */
    public function getArrayData($profileId)
    {
        return $this->defineGetArrayDataQuery($profileId)->getQuery()
            ->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * Find recently logged in administrators
     *
     * @param integer $length List length OPTIONAL
     *
     * @return array
     */
    public function findRecentAdmins($length = self::DEFAULT_RECENT_ADMINS_LENGTH)
    {
        return $this->defineFindRecentAdminsQuery($length)->getResult();
    }

    /**
     * Find user with same login
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \XLite\Model\Profile|void
     */
    public function findUserWithSameLogin(ProfileEntity $profile)
    {
        return $this->defineFindUserWithSameLoginQuery($profile)->getSingleResult();
    }

    /**
     * Find user with same login
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \XLite\Model\Profile|void
     */
    public function checkRegisteredUserWithSameLogin(ProfileEntity $profile)
    {
        return (bool) $this->defineCheckRegisteredUserWithSameLogin($profile)->getSingleScalarResult();
    }

    /**
     * Find the count of administrator accounts
     *
     * @return integer
     */
    public function findCountOfAdminAccounts()
    {
        return (int) $this->defineFindCountOfAdminAccountsQuery()->getSingleScalarResult();
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity|void
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (
            isset($data['login'])
            && (isset($data['order_id'])
                && ($data['order_id'] == 0 && $data['order_id'] !== 'null')
                || count($data) === 1
            )
        ) {
            // N.B. Thats loading only customerss
            $entity = $this->defineOneByRecord($data['login'])->getSingleResult();
        } else {
            $entity = parent::findOneByRecord($data, $parent);
        }

        return $entity;
    }

    /**
     * Find anonymous profile by another profile
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\Profile
     */
    public function findOneAnonymousByProfile(ProfileEntity $profile)
    {
        return $this->defineFindOneAnonymousByProfileQuery($profile)->getSingleResult();
    }

    /**
     * Iterate by customers
     *
     * @return \Iterator
     */
    public function iterateByCustomers()
    {
        return $this->defineIterateByCustomersQuery()->iterate();
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function defineRemoveDataQueryBuilder($position)
    {
        return $this->defineIterateByCustomersQuery()
            ->setMaxResults(1000000000);
    }

    /**
     * Generate password
     *
     * @return string
     */
    public function generatePassword()
    {
        return \XLite\Core\Operator::getInstance()->generateToken(static::PASSWORD_LENGTH, static::$passwordChars);
    }

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $cnd = $this->searchState['currentSearchCnd'];

        if (isset($cnd->{static::SEARCH_ORDER_ID}) && !$cnd->{static::SEARCH_ORDER_ID}) {
            $cnd->{static::SEARCH_ORDER_ID} = 0;
        }

        parent::processConditions();
    }

    /**
     * prepareCndProfileId
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->bindAndCondition('p.profile_id', $value);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndAnonymous(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->bindAnonymous();
        } else {
            $queryBuilder->bindRegistered();
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->bindOrder($value);
        } else {
            $queryBuilder->bindVisible();
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndReferer(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->bindAndCondition('p.referer', '%' . $value . '%', 'LIKE');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($this->searchState['currentSearchCnd']->{static::SEARCH_USER_TYPE} !== 'A') {
            $queryBuilder->bindMembership($value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndRoles(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->bindRoles($value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndPermissions(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->bindPermissions($value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndLanguage(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->bindAndCondition('p.language', $value);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndPattern(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->bindPattern($value);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndPhone(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->bindFieldAndCondition('name', '%' . $value . '%', 'LIKE');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndLogin(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->bindOrCondition('p.login', '%' . $value . '%', 'LIKE');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndAndLogin(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->bindAndCondition('p.login', '%' . $value . '%', 'LIKE');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder
                ->leftJoin('addresses.country', 'country')
                ->bindAndCondition('country.code', $value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndState(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($this->searchState['currentSearchCnd']->{static::SEARCH_COUNTRY}) && $value) {
            $queryBuilder
                ->leftJoin('addresses.state', 'state')
                ->bindAndCondition('state.state_id', $value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndCustomState(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($this->searchState['currentSearchCnd']->{static::SEARCH_COUNTRY}) && $value) {
            $queryBuilder->bindFieldAndCondition('custom_state', $value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndAddress(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->bindAddress($value);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndUserType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $roles = [];

        if (!is_array($value)) {
            $value = [$value];
        }

        $condition = $queryBuilder->expr()->orX();

        foreach ($value as $selectedType) {
            if (is_numeric($selectedType)) {
                $roles[] = $selectedType;
            } elseif ($selectedType === 'C') {
                $condition->add(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->getRegisteredCondition(),
                        $queryBuilder->getCustomerCondition()
                    )
                );

                $queryBuilder->setParameter('anonymous', true)
                    ->setParameter('adminAccessLevel', \XLite\Core\Auth::getInstance()->getAdminAccessLevel());
            } elseif ($selectedType === 'N') {
                $condition->add(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->getAnonymousCondition(),
                        $queryBuilder->getCustomerCondition()
                    )
                );

                $queryBuilder->setParameter('anonymous', true)
                    ->setParameter('adminAccessLevel', \XLite\Core\Auth::getInstance()->getAdminAccessLevel());
            }
        }

        if ($roles) {
            $condition->add(
                $queryBuilder->expr()->andX(
                    $queryBuilder->getRegisteredCondition(),
                    $queryBuilder->getAdminCondition(),
                    $queryBuilder->getRolesCondition($roles)
                )
            );

            $queryBuilder->linkLeft('p.roles')
                ->setParameter('anonymous', true)
                ->setParameter('adminAccessLevel', \XLite\Core\Auth::getInstance()->getAdminAccessLevel());
        }

        if ($condition->count()) {
            $queryBuilder->andWhere($condition);
        }
    }

    /**
     * prepareCndDateType
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndDateType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $dateRange = $this->getDateRange();

        if ($dateRange !== null && in_array($value, ['R', 'L'], true)) {
            $field = $value === 'R' ? 'p.added' : 'p.last_login';
            $queryBuilder->bindMacroDate($field, $dateRange->startDate, $dateRange->endDate);
        }
    }

    /**
     * getDateRange
     *
     * :FIXME: simplify
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getDateRange()
    {
        $result = null;

        $paramDatePeriod = static::SEARCH_DATE_PERIOD;
        if (isset($this->searchState['currentSearchCnd']->$paramDatePeriod)) {
            $datePeriod = $this->searchState['currentSearchCnd']->$paramDatePeriod;
            $startDate = null;
            $endDate = \XLite\Core\Converter::time();

            if ($datePeriod === 'M') {
                $startDate = mktime(0, 0, 0, date('n', $endDate), 1, date('Y', $endDate));
            } elseif ($datePeriod === 'W') {
                $startDate = \XLite\Core\Converter::getDayStart($endDate - (date('w', $endDate) * 86400));
            } elseif ($datePeriod === 'D') {
                $startDate = \XLite\Core\Converter::getDayStart($endDate);
            } elseif ($datePeriod === 'C') {
                $paramStartDate = static::SEARCH_START_DATE;
                $paramEndDate = static::SEARCH_END_DATE;
                $paramDateRange = static::SEARCH_DATE_RANGE;

                if (
                    !empty($this->searchState['currentSearchCnd']->$paramStartDate)
                    && !empty($this->searchState['currentSearchCnd']->$paramEndDate)
                ) {
                    $tmpDate = strtotime($this->searchState['currentSearchCnd']->$paramStartDate);

                    if ($tmpDate !== false) {
                        $startDate = \XLite\Core\Converter::getDayStart($tmpDate);
                    }

                    $tmpDate = strtotime($this->searchState['currentSearchCnd']->$paramEndDate);

                    if ($tmpDate !== false) {
                        $endDate = \XLite\Core\Converter::getDayEnd($tmpDate);
                    }
                } elseif (!empty($this->searchState['currentSearchCnd']->$paramDateRange)) {
                    [$startDate, $endDate] = \XLite\View\FormField\Input\Text\DateRange::convertToArray(
                        $this->searchState['currentSearchCnd']->$paramDateRange
                    );
                }
            }

            if (
                $startDate !== null
                && $startDate !== false
                && $endDate !== false
            ) {
                $result = new \XLite\Core\CommonCell();
                $result->startDate = $startDate;
                $result->endDate = $endDate;
            }
        }

        return $result;
    }

    /**
     * Prepare fields for fullname value (for 'order by')
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $fieldName    Field name
     *
     * @return void
     */
    protected function prepareOrderByAddressField(\Doctrine\ORM\QueryBuilder $queryBuilder, $fieldName)
    {
        $addressField = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
            ->findOneBy(['serviceName' => $fieldName]);

        $queryBuilder
            ->leftJoin(
                'addresses.addressFields',
                'orderby_field_value_' . $fieldName,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'orderby_field_value_' . $fieldName . '.addressField = :' . $fieldName
            )->setParameter($fieldName, $addressField);
    }

    /**
     * Generate fullname by firstname and lastname values
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     *
     * @return void
     */
    protected function prepareCndOrderByFullname(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $this->prepareOrderByAddressField($queryBuilder, 'firstname');
        $this->prepareOrderByAddressField($queryBuilder, 'lastname');

        $queryBuilder
            ->addSelect(
                'CONCAT(CONCAT(orderby_field_value_firstname.value, \' \'),
            orderby_field_value_lastname.value) as fullname'
            );
    }

    /**
     * order by non-anonymous priority
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param string                     $order        Sort param
     *
     * @return void
     */
    protected function prepareCndOrderByAccessLevel(\Doctrine\ORM\QueryBuilder $queryBuilder, string $order)
    {
        $queryBuilder
            ->addOrderBy('p.anonymous', $order === 'asc' ? 'desc' : 'asc');
    }

    /**
     * prepareCndOrderBy
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param array                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        [$sort, $order] = $this->getSortOrderValue($value);

        if ($sort === \XLite\View\ItemsList\Model\Profile::SORT_BY_MODE_NAME) {
            $this->prepareCndOrderByFullname($queryBuilder);
        }

        parent::prepareCndOrderBy($queryBuilder, $value);

        if ($sort === \XLite\View\ItemsList\Model\Profile::SORT_BY_MODE_ACCESS_LEVEL) {
            $this->prepareCndOrderByAccessLevel($queryBuilder, $order);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param string                     $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->bindAndCondition('p.status', $value);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param string                     $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndOnlyReal(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->bindVisible();
        }
    }

    /**
     * Define query for findRecentAdmins() method
     *
     * @param integer $length List length OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindRecentAdminsQuery($length)
    {
        return $this->createQueryBuilder()
            ->bindAdmin()
            ->bindLogged()
            ->addOrderBy('p.last_login')
            ->setMaxResults($length);
    }

    /**
     * Define query for findUserWithSameLogin() method
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindUserWithSameLoginQuery(ProfileEntity $profile)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->bindSameLogin($profile);

        if ($profile->getOrder()) {
            $queryBuilder->bindOrder($profile->getOrder());
        } else {
            $queryBuilder->bindRegistered();
        }

        return $queryBuilder;
    }

    /**
     * Define query for findUserWithSameLogin() method
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCheckRegisteredUserWithSameLogin(ProfileEntity $profile)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->select('COUNT(DISTINCT p.profile_id)')
            ->bindSameLogin($profile);

        if ($profile->getOrder()) {
            $queryBuilder->bindOrder($profile->getOrder());
        } else {
            $queryBuilder->bindRegistered();
        }

        return $queryBuilder;
    }

    /**
     * All admin profile
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function findAllAdminAccounts()
    {
        return $this->defineFindAllAdminAccountsQuery()
            ->getResult();
    }

    /**
     * All customer profile
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function findAllCustomerAccounts()
    {
        return $this->defineFindAllCustomerAccountsQuery()
            ->getResult();
    }

    /**
     * Define query for findAllAdminAccounts()
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineFindAllAdminAccountsQuery()
    {
        return $this->createQueryBuilder()
            ->bindAdmin()
            ->bindRegistered();
    }

    /**
     * Define query for findFindAllCustomerAccounts()
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineFindAllCustomerAccountsQuery()
    {
        return $this->createQueryBuilder()
            ->bindCustomer()
            ->bindRegistered();
    }

    /**
     * Define query for findCountOfAdminAccounts()
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineFindCountOfAdminAccountsQuery()
    {
        return $this->createQueryBuilder()
            ->selectCount()
            ->bindAdmin()
            ->bindAndCondition('p.status', ProfileEntity::STATUS_ENABLED);
    }

    /**
     * Define query for findByLoginPassword() method
     *
     * @param string  $login    User's login
     * @param string  $password User's password
     * @param integer $orderId  Order ID related to the profile OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByLoginPasswordQuery($login, $password, $orderId)
    {
        $conditions = [
            'login'  => $login,
            'status' => ProfileEntity::STATUS_ENABLED,
        ];

        $queryBuilder = $this->createQueryBuilder();

        if ($password !== null) {
            $conditions['password'] = $password;
        }

        if ($orderId) {
            $queryBuilder->bindOrder($orderId);
        } else {
            $queryBuilder->bindRegistered();
        }

        return $queryBuilder->mapAndConditions($conditions);
    }

    /**
     * @param integer $profileId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetArrayDataQuery($profileId)
    {
        $qb = $this->createQueryBuilder();

        return $qb->where('p.profile_id = :profileId')
            ->setParameter('profileId', $profileId)
            ->setMaxResults(1);
    }

    /**
     * Collect alternative identifiers by record
     *
     * @param array $data Record
     *
     * @return boolean|array(mixed)
     */
    protected function collectAlternativeIdentifiersByRecord(array $data)
    {
        $result = parent::collectAlternativeIdentifiersByRecord($data);

        if (
            !$result
            && !empty($data['login'])
            && isset($data['order_id'])
            && (!$data['order_id'] || $data['order_id'] === 'null')
        ) {
            $result = [
                'login' => $data['login'],
                'order' => null,
            ];
        }

        return $result;
    }

    /**
     * Link loaded entity to parent object
     *
     * @param \XLite\Model\AEntity $entity      Loaded entity
     * @param \XLite\Model\AEntity $parent      Entity parent callback
     * @param array                $parentAssoc Entity mapped property method
     *
     * @return void
     */
    protected function linkLoadedEntity(\XLite\Model\AEntity $entity, \XLite\Model\AEntity $parent, array $parentAssoc)
    {
        if (
            $parent instanceof \XLite\Model\Order
            && !$parentAssoc['mappedSetter']
            && $parentAssoc['setter'] === 'setProfile'
        ) {
            // Add order to profile if this profile - copy of original profile
            $parentAssoc['mappedSetter'] = 'setOrder';
        }

        parent::linkLoadedEntity($entity, $parent, $parentAssoc);
    }


    /**
     * Load fixture
     *
     * @param array                $record      Record
     * @param \XLite\Model\AEntity $parent      Entity parent callback OPTIONAL
     * @param array                $parentAssoc Entity mapped propery method OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function loadFixture(
        array $record,
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = []
    ) {
        $entity = parent::loadFixture($record, $parent, $parentAssoc);

        $entity->updateSearchFakeField();
    }

    /**
     * Define query for findOneByRecord () method
     *
     * @param string $login Login
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByRecord($login)
    {
        return $this->createQueryBuilder()
            ->bindCustomer()
            ->bindAndCondition('p.login', $login);
    }

    /**
     * Define query for findOneAnonymousByProfile() method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneAnonymousByProfileQuery(ProfileEntity $profile)
    {
        return $this->createQueryBuilder()
            ->bindAnonymous()
            ->bindAndCondition('p.login', $profile->getLogin());
    }

    /**
     * Define query for iterateByCustomers() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineIterateByCustomersQuery()
    {
        return $this->createPureQueryBuilder()
            ->bindCustomer();
    }

    // {{{ Export routines

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        return parent::defineCountForExportQuery()->bindVisible();
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineExportIteratorQueryBuilder($position)
    {
        return parent::defineExportIteratorQueryBuilder($position)->bindVisible();
    }

    // }}}

    // {{{ Import

    /**
     * Define import query builder
     *
     * @param array $conditions Conditions
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByImportConditionsQueryBuilder(array $conditions)
    {
        $qb = parent::defineFindOneByImportConditionsQueryBuilder($conditions);

        $qb->bindVisible();

        if (!\XLite\Core\Auth::getInstance()->isAdminProfilesManager()) {
            $qb->bindCustomer();
        }

        return $qb;
    }

    // }}}

    // {{{ findProfilesByTerm

    /**
     * Find vendors by term
     *
     * @param string  $term  Term
     * @param integer $limit Limit OPTIONAL
     *
     * @return array
     */
    public function findProfilesByTerm($term, $limit = null)
    {
        $queryBuilder = $this->defineFindProfilesByTerm($term, $limit);

        return $queryBuilder->getOnlyEntities();
    }

    /**
     * define query builder for search vendors by term
     *
     * @param string  $term  Term
     * @param integer $limit Limit OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindProfilesByTerm($term, $limit = null)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder->bindVisible();

        $this->prepareCndPattern($queryBuilder, $term);

        $queryBuilder->addGroupBy('p.profile_id');

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder;
    }

    // }}}

    // {{{

    /**
     * @param \XLite\Model\Profile $profile
     * @param null|string          $email
     * @param null|string          $oldEmail
     */
    public function updateOrderProfileEmailByOrigProfile($profile, $email = null, $oldEmail = null)
    {
        $email = $email ?: $profile->getLogin();
        $oldEmail = $oldEmail ?: $profile->getLogin();

        $profiles = $this->createPureQueryBuilder('p')
            ->select('p.profile_id')
            ->linkInner('p.order', 'o')
            ->andWhere('o.orig_profile = :profile')
            ->setParameter('profile', $profile)
            ->andWhere('p.login = :oldEmail')
            ->setParameter('oldEmail', $oldEmail)
            ->getQuery()->getArrayResult();

        $ids = array_map(static function ($item) {
            return $item['profile_id'];
        }, $profiles);

        if (!empty($ids)) {
            $expr = new \Doctrine\ORM\Query\Expr();
            $this->createPureQueryBuilder('p')
                ->update($this->_entityName, 'p')
                ->set('p.login', ':email')
                ->setParameter('email', $email)
                ->where($expr->in('p.profile_id', $ids))
                ->execute();
        }
    }
    // }}}


    /**
     * @return int
     */
    public function countPendingMemberships()
    {
        /** @var \XLite\Model\QueryBuilder\Profile $qb */
        $qb = $this->createQueryBuilder();
        $qb->bindCustomer();
        $qb->andWhere('p.pending_membership IS NOT NULL');
        $qb->selectCount();

        return $qb->getSingleScalarResult();
    }

    // UserRepositoryInterface

    public function loadUserByIdentifier(string $identifier): ?ProfileEntity
    {
        return $this->findByLogin($identifier);
    }

    public function upgradePassword(ProfileEntity $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);
        $this->update($user, [], true);
    }
}
