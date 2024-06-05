<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Autocomplete controller
 */
class Autocomplete extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Some constants
     */
    public const PROFILES_MAX_RESULTS = 9;

    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $result = parent::checkACL();

        if (!$result) {
            $dictionary = \XLite\Core\Request::getInstance()->dictionary;

            $permissions = $this->getDictionaryPermissions();

            if (!empty($permissions[$dictionary])) {
                foreach ($permissions[$dictionary] as $p) {
                    if (\XLite\Core\Auth::getInstance()->isPermissionAllowed($p)) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $content = json_encode($this->data);

        $xLite = \XLite::getInstance();
        $xLite->addHeader('Content-Type', 'application/json; charset=UTF-8');
        $xLite->addHeader('Content-Length', strlen($content));
        $xLite->addHeader('ETag', md5($content));

        $xLite->addContent($content);
    }

    /**
     * Get list of possible dictionary permissions
     *
     * @return array
     */
    protected function getDictionaryPermissions()
    {
        return [
            'attributeOption' => [
                'manage catalog',
            ],
            'profiles'        => [
                'manage orders',
            ],
            'categories'      => [
                'manage catalog'
            ],
        ];
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $dictionary = \XLite\Core\Request::getInstance()->dictionary;

        if ($dictionary) {
            $method = 'assembleDictionary' . \Includes\Utils\Converter::convertToUpperCamelCase($dictionary);
            if (method_exists($this, $method)) {
                // Method name assmbled from 'assembleDictionary' + dictionary input argument
                $data       = $this->$method((string) \XLite\Core\Request::getInstance()->term);
                $this->data = $this->processData($data);
            }
        }

        $this->silent = true;
    }

    /**
     * Process data
     *
     * @param array $data Key-value data
     *
     * @return array
     */
    protected function processData(array $data)
    {
        $list = [];

        foreach ($data as $k => $v) {
            $list[] = [
                'label' => $v,
                'value' => $k,
            ];
        }

        return $list;
    }

    /**
     * Assemble dictionary - conversation recipient
     *
     * @param string $term Term
     *
     * @return array
     */
    protected function assembleDictionaryAttributeOption($term)
    {
        $cnd = new \XLite\Core\CommonCell();
        if ($term) {
            $cnd->{\XLite\Model\Repo\AttributeOption::SEARCH_NAME} = $term;
        }

        $id = (int) \XLite\Core\Request::getInstance()->id;
        if ($id) {
            $cnd->{\XLite\Model\Repo\AttributeOption::SEARCH_ATTRIBUTE} = $id;
        }

        $cnd->{\XLite\Model\Repo\AttributeOption::P_ORDER_BY} = ['a.position', 'ASC'];

        $list = [];
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\AttributeOption')->search($cnd) as $a) {
            $name        = $a->getName();
            $list[$name] = $name;
        }

        return $list;
    }

    /**
     * Assemble dictionary - conversation recipient
     *
     * @param string $term Term
     *
     * @return array
     */
    protected function assembleDictionaryProfiles($term)
    {
        $profiles = \XLite\Core\Database::getRepo('\XLite\Model\Profile')
            ->findProfilesByTerm($term, static::PROFILES_MAX_RESULTS);

        return $this->packProfilesData($profiles);
    }

    /**
     * Assemble dictionary - conversation recipient
     *
     * @param string $term Term
     *
     * @return array
     */
    protected function assembleDictionaryCategories($term)
    {
        $profiles = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->getFilteredCategoriesAsDTO($term);

        return array_reduce($profiles, static function ($acc, $item) {
            $acc[$item['id']] = $item['fullNameHtml'] ?? $item['fullName'];
            return $acc;
        }, []);
    }

    /**
     * Get certain data from profile array for new array
     *
     * @param array $profiles Array of profiles
     *
     * @return array
     */
    protected function packProfilesData(array $profiles)
    {
        $result = [];

        if ($profiles) {
            foreach ($profiles as $k => $profile) {
                $result[$profile->getProfileId()] = $profile->getName() . ' (' . $profile->getLogin() . ')';
            }
        }

        return $result;
    }
}
