<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model\Repo;

use XLite\Core\ConfigCell;
use XLite\Core\Database;

/**
 * Magic360 configuration registry
 */
class Config extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['profile', 'name'],
    ];

    /**
     * Find all settings by profile name
     *
     * @param string $profile Profile name
     *
     * @return array
     */
    public function findByProfile($profile)
    {
        $result = $this->getByProfile($profile, true, true);
        if ($result) {
            foreach ($result as $k => $v) {
                if (empty($v->type)) {
                    unset($result[$k]);
                }
            }
        }

        return $result ?: [];
    }

    /**
     * Get the list of options of the specified profile
     *
     * @param string  $profile      Profile
     * @param boolean $force        Force OPTIONAL
     * @param boolean $doNotProcess Do not process OPTIONAL
     *
     * @return array
     */
    public function getByProfile($profile, $force = false, $doNotProcess = false)
    {
        $data = null;
        if (!$force) {
            $data = $this->getFromCache('profile', ['profile' => $profile]);
        }
        if (!isset($data)) {
            $data = $this->findBy(['profile' => $profile], ['orderby' => 'asc']);
            if (!$doNotProcess) {
                $data = $this->processOptions($data);
                $this->saveToCache($data, 'profile', ['profile' => $profile]);
            }
        }

        return $data;
    }

    /**
     * Preprocess options and transform its to the hierarchy of \XLite\Core\ConfigCell objects
     *
     * @param array $data Array of options data gathered from the database
     *
     * @return \XLite\Core\ConfigCell
     */
    public function processOptions($data)
    {
        $config = new ConfigCell();

        foreach ($data as $option) {

            $profile = $option->getProfile();
            $name    = $option->getName();
            $value   = $option->getValue();

            if (!isset($config->$profile)) {
                $config->$profile = new ConfigCell();
            }

            $config->$profile->$name = $value;

        }

        return $config;
    }

    /**
     * Find settings by profile name and option names
     *
     * @param string $profile Profile name
     * @param array  $names   Array of option names
     *
     * @return array
     */
    public function findByProfileAndNames($profile, $names = [])
    {
        $result = $this->getByProfile($profile, true, true);
        if ($result) {
            foreach ($result as $k => $v) {
                if (!in_array($v->name, $names)) {
                    unset($result[$k]);
                }
            }
        }

        return $result ?: [];
    }

    /**
     * Get the list of all options
     *
     * @param boolean $force Do not use cache OPTIONAL
     *
     * @return array
     */
    public function getAllOptions($force = false)
    {
        $data = null;
        if (!$force) {
            $data = $this->getFromCache('all');
        }
        if (!isset($data)) {
            $data = $this->createQueryBuilder()->getResult();
            $data = $this->detachList($data);
            $data = $this->processOptions($data);
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Get the list of editable options
     *
     * @param boolean $force Do not use cache OPTIONAL
     *
     * @return array
     */
    public function getEditableAndActiveOptions($force = false)
    {
        $data = null;
        if (!$force) {
            $data = $this->getFromCache('editable');
        }
        if (!isset($data)) {
            $data    = $this->createQueryBuilder()->getResult();
            $data    = $this->detachList($data);
            $exclude = ['separator', 'hidden'];
            foreach ($data as $key => $option) {
                if ($option->getStatus() == \Qualiteam\SkinActMagicImages\Model\Config::OPTION_IS_INACTIVE || in_array($option->getType(), $exclude)) {
                    unset($data[$key]);
                }
            }
            $data = $this->processOptions($data);
            $this->saveToCache($data, 'editable');
        }

        return $data;
    }

    /**
     * Create new option / Update option value
     *
     * @param array $data Option data in the following format
     *
     * @return void
     * @throws \Exception
     */
    public function createOption($data)
    {
        $fields = [
            'profile'       => 1,
            'name'          => 1,
            'value'         => 1,
            'type'          => 0,
            'orderby'       => 0,
            'default_value' => 0,
            'status'        => 0,
        ];

        $errorFields = [];

        foreach ($fields as $field => $required) {
            if (isset($data[$field])) {
                $fields[$field] = $data[$field];
            } elseif ($required) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new \Exception(
                'createOptions() failed: The following required fields are missed: '
                . implode(', ', $errorFields)
            );
        }

        if (isset($fields['type']) && !$this->isValidOptionType($fields['type'])) {
            throw new \Exception('createOptions() failed: Wrong option type: ' . $type);
        }

        $option = $this->findOneBy(['name' => $fields['name'], 'profile' => $fields['profile']]);

        if ($option) {
            // Existing option
            $option->setValue($fields['value']);
        } else {
            // Create a new option
            $option = new \Qualiteam\SkinActMagicImages\Model\Config();
            $option->map($fields);
            Database::getEM()->persist($option);
        }

        Database::getEM()->flush();

    }

    /**
     * Check if option type is a valid
     *
     * @param string $optionType Option type
     *
     * @return boolean
     */
    protected function isValidOptionType($optionType)
    {
        $simple = in_array(
            $optionType,
            [
                '',
                'text',
                'radio',
                'select',
                'separator',
                'textarea',
                'checkbox',
                'serialized',
                'hidden',
            ]
        );

        if (!$simple && preg_match('/^XLite\\\(Module\\\.+\\\)?View\\\FormField\\\/Ss', $optionType)) {
            $simple = true;
        }

        return $simple;
    }

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list             = parent::defineCacheCells();
        $list['all']      = [];
        $list['profile']  = [
            self::ATTRS_CACHE_CELL => ['profile'],
        ];
        $list['editable'] = [];

        return $list;
    }
}
