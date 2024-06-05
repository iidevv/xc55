<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XLite\Core\Auth;
use XLite\Model\Role;
use XLite\Model\Role\Permission;

/**
 * User profiles
 */
class Profile extends \XLite\View\ItemsList\Model\Table
{
    /**
     * List of search params for this widget (cache)
     *
     * @var array
     */
    protected $searchParams;

    /**
     * Widget param names
     */
    public const PARAM_PATTERN      = 'pattern';
    public const PARAM_USER_TYPE    = 'user_type';
    public const PARAM_MEMBERSHIP   = 'membership';
    public const PARAM_COUNTRY      = 'country';
    public const PARAM_STATE        = 'state';
    public const PARAM_CUSTOM_STATE = 'customState';
    public const PARAM_ADDRESS      = 'address';
    public const PARAM_PHONE        = 'phone';
    public const PARAM_DATE_TYPE    = 'date_type';
    public const PARAM_DATE_PERIOD  = 'date_period';
    public const PARAM_DATE_RANGE   = 'dateRange';
    public const PARAM_STATUS       = 'status';
    public const PARAM_LOGIN        = 'login';

    /**
     * Allowed sort criterion
     */
    public const SORT_BY_MODE_LOGIN        = 'p.login';
    public const SORT_BY_MODE_NAME         = 'fullname';
    public const SORT_BY_MODE_ACCESS_LEVEL = 'p.access_level';
    public const SORT_BY_MODE_CREATED      = 'p.added';
    public const SORT_BY_MODE_LAST_LOGIN   = 'p.last_login';

    public const MULTISELECT_TYPES = [
        \XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP,
        \XLite\Model\Repo\Profile::SEARCH_USER_TYPE
    ];

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_LOGIN        => 'Login/Email',
            static::SORT_BY_MODE_NAME         => 'Name',
            static::SORT_BY_MODE_ACCESS_LEVEL => 'Access level',
            static::SORT_BY_MODE_CREATED      => 'Created',
            static::SORT_BY_MODE_LAST_LOGIN   => 'Last login',
        ];

        parent::__construct($params);
    }

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'profile_list';

        return $list;
    }

    /**
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'profile_list';
    }

    /**
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\Profile\Admin\Main';
    }

    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'login' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Login/E-mail'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'profile',
                static::COLUMN_SORT     => static::SORT_BY_MODE_LOGIN,
                static::COLUMN_ORDERBY  => 100,
            ],
            'name' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'address_book',
                static::COLUMN_MAIN     => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_NAME,
                static::COLUMN_ORDERBY  => 200,
            ],
            'access_level' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Access level'),
                static::COLUMN_SORT     => static::SORT_BY_MODE_ACCESS_LEVEL,
                static::COLUMN_ORDERBY  => 300,
            ],
            'orders_count' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Orders'),
                static::COLUMN_TEMPLATE => 'profiles/parts/cell/orders.twig',
                static::COLUMN_ORDERBY  => 400,
            ],
            'added' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Created'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/profile/cell.added.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_CREATED,
                static::COLUMN_ORDERBY  => 500,
            ],
            'last_login' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Last login'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/profile/cell.last_login.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_LAST_LOGIN,
                static::COLUMN_ORDERBY  => 600,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_LAST_LOGIN;
    }

    /**
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Profile';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'profiles/style.css';

        return $list;
    }

    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('profile', null, ['mode' => 'register']);
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add user';
    }

    /**
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        // Admin user cannot remove own account
        return parent::isAllowEntityRemove($entity)
            && Auth::getInstance()->getProfile()->getProfileId() !== $entity->getProfileId();
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' profiles';
    }

    /**
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if ($column[static::COLUMN_CODE] == 'access_level' && $entity && $entity->getAnonymous()) {
            $class = trim($class . ' anonymous');
        }

        return $class;
    }

    /**
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\Profile';
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Profile::SEARCH_PATTERN      => static::PARAM_PATTERN,
            \XLite\Model\Repo\Profile::SEARCH_USER_TYPE    => static::PARAM_USER_TYPE,
            \XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP   => static::PARAM_MEMBERSHIP,
            \XLite\Model\Repo\Profile::SEARCH_COUNTRY      => static::PARAM_COUNTRY,
            \XLite\Model\Repo\Profile::SEARCH_STATE        => static::PARAM_STATE,
            \XLite\Model\Repo\Profile::SEARCH_CUSTOM_STATE => static::PARAM_CUSTOM_STATE,
            \XLite\Model\Repo\Profile::SEARCH_ADDRESS      => static::PARAM_ADDRESS,
            \XLite\Model\Repo\Profile::SEARCH_PHONE        => static::PARAM_PHONE,
            \XLite\Model\Repo\Profile::SEARCH_DATE_TYPE    => static::PARAM_DATE_TYPE,
            \XLite\Model\Repo\Profile::SEARCH_DATE_PERIOD  => static::PARAM_DATE_PERIOD,
            \XLite\Model\Repo\Profile::SEARCH_DATE_RANGE   => static::PARAM_DATE_RANGE,
            \XLite\Model\Repo\Profile::SEARCH_STATUS       => static::PARAM_STATUS,
            \XLite\Model\Repo\Profile::SEARCH_AND_LOGIN    => static::PARAM_LOGIN,
        ];
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PATTERN         => new \XLite\Model\WidgetParam\TypeString('Pattern', ''),
            static::PARAM_USER_TYPE       => new \XLite\Model\WidgetParam\TypeSet('Type', '', false, ['', 'A', 'C']),
            static::PARAM_MEMBERSHIP      => new \XLite\Model\WidgetParam\TypeCollection('Membership', []),
            static::PARAM_COUNTRY         => new \XLite\Model\WidgetParam\TypeString('Country', ''),
            static::PARAM_STATE           => new \XLite\Model\WidgetParam\TypeInt('State', null),
            static::PARAM_CUSTOM_STATE    => new \XLite\Model\WidgetParam\TypeString('State name (custom)', ''),
            static::PARAM_ADDRESS         => new \XLite\Model\WidgetParam\TypeString('Address', ''),
            static::PARAM_PHONE           => new \XLite\Model\WidgetParam\TypeString('Phone', ''),
            static::PARAM_DATE_TYPE       => new \XLite\Model\WidgetParam\TypeSet('Date type', '', false, ['', 'R', 'L']),
            static::PARAM_DATE_PERIOD     => new \XLite\Model\WidgetParam\TypeSet('Date period', '', false, ['', 'M', 'W', 'D', 'C']),
            static::PARAM_DATE_RANGE      => new \XLite\Model\WidgetParam\TypeString('Date range', null),
            static::PARAM_STATUS          => new \XLite\Model\WidgetParam\TypeString('Status', null),
            static::PARAM_LOGIN           => new \XLite\Model\WidgetParam\TypeString('Login', null),
        ];
    }

    /**
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * @return array Array of ids
     */
    protected function getPermittedUserTypes()
    {
        $permittedUserTypes = Auth::getInstance()->isPermissionAllowed('manage users')
            ? ['N', 'C']
            : [];
        $auth = Auth::getInstance();
        $isRoot = $auth->hasRootAccess();

        if ($auth->isPermissionAllowed('manage admins')) {
            $adminRoles         = array_map(
                static function ($role) {
                    return $role->getId();
                },
                array_filter(
                    \XLite\Core\Database::getRepo('XLite\Model\Role')->findAll(),
                    static fn(Role $role): bool => ($isRoot || !$role->isPermissionAllowed(Permission::ROOT_ACCESS))
                )
            );
            $permittedUserTypes = array_merge($adminRoles, $permittedUserTypes);
        }

        return $permittedUserTypes;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\XLite\Model\Repo\Profile::P_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                if (in_array($modelParam, self::MULTISELECT_TYPES)) {
                    $paramValue = explode(',', $paramValue);
                } else {
                    $paramValue = trim($paramValue);
                }
            }

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }
        $result->{\XLite\Model\Repo\Profile::SEARCH_ONLY_REAL} = true;

        if ($result->{\XLite\Model\Repo\Profile::SEARCH_COUNTRY}) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find(
                $result->{\XLite\Model\Repo\Profile::SEARCH_COUNTRY}
            );
            if (!$country || !$country->hasStates()) {
                $result->{\XLite\Model\Repo\Profile::SEARCH_STATE} = null;
            }
            if (!$country || $country->hasStates()) {
                $result->{\XLite\Model\Repo\Profile::SEARCH_CUSTOM_STATE} = null;
            }
        }

        if (filter_var($result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN}, FILTER_VALIDATE_EMAIL)) {
            $result->{\XLite\Model\Repo\Profile::SEARCH_AND_LOGIN} = $result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN};
            $result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN}   = null;
        } else {
            $result->{\XLite\Model\Repo\Profile::SEARCH_AND_LOGIN} = null;
        }

        if ($result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP}) {
            $membershipCondition = $result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP};

            if (is_array($membershipCondition)) {
                $membershipIds = array_reduce(
                    $membershipCondition,
                    static function ($carry, $item) {
                        $item = explode('_', $item);
                        if (count($item) == 2) {
                            $carry[$item[0]][] = $item[1];
                        }

                        return $carry;
                    },
                    []
                );

                $result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP} = $membershipIds;
            }
        }

        $permittedUserTypes = $this->getPermittedUserTypes();

        if (
            isset($result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0])
            && $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0] !== ''
        ) {
            $userTypes                                             = array_filter(
                $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE},
                static function ($type) use ($permittedUserTypes) {
                    return in_array($type, $permittedUserTypes);
                }
            );
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $userTypes;
        } elseif (!Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)) {
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $permittedUserTypes;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams[\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP] = $this->getParam(\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP);

        return $this->commonParams;
    }

    /**
     * @param integer              $date   Date
     * @param array                $column Column data
     * @param \XLite\Model\Profile $entity Profile
     *
     * @return string
     */
    protected function preprocessAdded($date, array $column, \XLite\Model\Profile $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Unknown');
    }

    /**
     * @param integer              $date   Date
     * @param array                $column Column data
     * @param \XLite\Model\Profile $entity Profile
     *
     * @return string
     */
    protected function preprocessLastLogin($date, array $column, \XLite\Model\Profile $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Never');
    }

    /**
     * @param integer              $accessLevel Access level
     * @param array                $column      Column data
     * @param \XLite\Model\Profile $entity      Profile
     *
     * @return string
     */
    protected function preprocessAccessLevel($accessLevel, array $column, \XLite\Model\Profile $entity)
    {
        if ($accessLevel == 0) {
            $result = $entity->getAnonymous()
                ? static::t('Anonymous')
                : static::t('Customer');

            if (
                $entity->getMembership()
                || $entity->getPendingMembership()
            ) {
                $result .= ' (';
            }

            if ($entity->getMembership()) {
                $result .= $entity->getMembership()->getName();
            }

            if ($entity->getPendingMembership()) {
                if ($entity->getMembership()) {
                    $result .= ', ';
                }

                $result .= static::t('requested for') . ' '
                    . $entity->getPendingMembership()->getName();
            }

            if (
                $entity->getMembership()
                || $entity->getPendingMembership()
            ) {
                $result .= ')';
            }
        } else {
            $result = static::t('Administrator');
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Profile $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $login = $entity ? $entity->getLogin() : null;

        $result = parent::removeEntity($entity);

        if ($result && $login) {
            \XLite\Core\Mailer::sendProfileDeleted($login);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/search_container.twig';
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'XLite\View\ItemsList\Model\Profile'
        ];
    }
}
