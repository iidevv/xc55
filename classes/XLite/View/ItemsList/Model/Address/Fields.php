<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Address;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Model\AEntity;

/**
 * Address fields items list
 */
class Fields extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'address_fields';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'address_fields';
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/address_fields/style.css';
        $list[] = 'address/fields/style.css';

        return $list;
    }

    /**
     * @param array                $column
     * @param AEntity $model
     */
    public function getHelpText(array $column, AEntity $model)
    {
        $helpText = 'Required state_id checkbox help text';
        $serviceName = $model->getServiceName();
        if ($this->shouldFieldAlwaysBeRequired($serviceName)) {
            $helpText = (
                $serviceName === 'email'
                    ? 'Email field must always be required'
                    : 'This field must always be required'
            );
        }
        return static::t($helpText);
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME     => static::t('Name'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS   => [
                    'required' => true,
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 30,
                ],
                static::COLUMN_ORDERBY  => 100,
            ],
            'serviceName' => [
                static::COLUMN_NAME     => static::t('Service name'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\AddressFieldsServiceName',
                static::COLUMN_TEMPLATE => 'items_list/model/table/field.twig',
                static::COLUMN_PARAMS   => ['required' => true],
                static::COLUMN_ORDERBY  => 200,
            ],
            'required' => [
                static::COLUMN_NAME     => static::t('Required'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
                static::COLUMN_TEMPLATE => 'address/fields/required.help.twig',
                static::COLUMN_PARAMS   => [],
                static::COLUMN_ORDERBY  => 300,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\AddressField';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        $found = false;
        foreach ($this->getPageData() as $model) {
            if ($model->getAdditional()) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Template for switcher action definition
     *
     * @return string
     */
    protected function getSwitcherActionTemplate()
    {
        return 'items_list/model/table/address_fields/switcher.twig';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('address_field');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New address field';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' address-fields';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\AddressField::CND_WITHOUT_CSTATE} = true;

        return $result;
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return parent::getEmptyListDir();
    }

    /**
     * Check - remove entity or not
     *
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity) && $entity->getAdditional();
    }

    /**
     * Check - switch entity or not
     *
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(AEntity $entity)
    {
        /* @var AddressField $entity */
        // Custom state is not allowed to switch off
        return parent::isAllowEntitySwitch($entity)
            && $entity->getServiceName() !== 'custom_state'
            && class_exists($entity->getSchemaClass());
    }

    /**
     * @param AEntity $entity
     *
     * @return boolean
     */
    protected function isShowSwitchWarning(AEntity $entity)
    {
        return !class_exists($entity->getSchemaClass())
            || $this->shouldFieldAlwaysBeEnabled($entity->getServiceName());
    }

    /**
     * @param AEntity $entity
     *
     * @return string
     */
    protected function getSwitchWarningMessage(AEntity $entity)
    {
        $serviceName = $entity->getServiceName();
        if ($this->shouldFieldAlwaysBeEnabled($serviceName)) {
            return static::t(
                $serviceName === 'email'
                ? 'Email field can not be disabled'
                : 'This field can not be disabled'
            );
        }
        $schemaClass = $entity->getSchemaClass();
        $moduleId = Module::getModuleIdByClassName($schemaClass);

        if (!$moduleId) {
            return static::t('The field can not be enabled');
        }

        $module = Manager::getRegistry()->getModule($moduleId);

        if ($module) {
            return static::t(
                'The field can not be enabled since the X addon is disabled.',
                [
                    'name' => $module->moduleName,
                    'link' => Manager::getRegistry()->getModuleServiceURL($moduleId),
                ]
            );
        }

        return static::t('The field can not be enabled since the corresponding addon is disabled.');
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column Column
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, AEntity $entity)
    {
        $result = null;

        if ($column[static::COLUMN_CODE] === 'serviceName') {
            // Right now admin cannot directly edit serviceName values for additional fields
            // and cannot change "Not required" state of "custom_state" field
            // TODO: refactor it
            $result = !$entity->getAdditional();
        } elseif ($column[static::COLUMN_CODE] === 'required') {
            $serviceName = $entity->getServiceName();
            $result = ($serviceName === 'state_id' || $this->shouldFieldAlwaysBeRequired($serviceName));
        }

        return $result ?? parent::isTemplateColumnVisible($column, $entity);
    }


    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, AEntity $entity)
    {
        $result = null;

        if ($column[static::COLUMN_CODE] === 'serviceName') {
            // Right now admin cannot directly edit serviceName values for additional fields
            // and cannot change "Not required" state of "custom_state" field
            // TODO: refactor it
            $result = $entity->getAdditional();
        } elseif ($column[static::COLUMN_CODE] === 'required') {
            $serviceName = $entity->getServiceName();
            return (
                !in_array($serviceName, ['custom_state', 'state_id'], true)
                && !$this->shouldFieldAlwaysBeRequired($serviceName)
            );
        }

        return $result ?? parent::isClassColumnVisible($column, $entity);
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
        parent::updateEntities();

        $enabled = false;
        $name = 'State';
        $custom_state_position = 0;
        foreach ($this->getPageData() as $entity) {
            if ($entity->getServiceName() == 'state_id') {
                $enabled = $entity->getEnabled();
                $name = $entity->getName();
                $custom_state_position = $entity->getPosition();
            }
        }

        $entity = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneByServiceName('custom_state');
        if ($entity) {
            $entity->setEnabled($enabled);
            $entity->setName($name);
            $entity->setPosition($custom_state_position);
        }
    }

    /**
     * Get forbidden values for service_name field
     *
     * @return array
     */
    protected function getForbiddenServiceNames()
    {
        return [];
    }

    /**
     * Post-validate new entity
     *
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);

        if (in_array($entity->getServiceName(), $this->getForbiddenServiceNames())) {
            $result = false;
            $this->errorMessages[] = static::t(
                'The service name X is reserved and cannot be used for an address field.',
                ['value' => $entity->getServiceName()]
            );
        }

        return $result;
    }

    /**
     * Returns true if the field with the given service name should always be enabled, false otherwise.
     *
     * @param string $serviceName Field service name
     */
    protected function shouldFieldAlwaysBeEnabled(string $serviceName): bool
    {
        return in_array($serviceName, [ 'email' ], true);
    }

    /**
     * Returns true if the field with the given service name should always be required, false otherwise.
     *
     * @param string $serviceName Field service name
     */
    protected function shouldFieldAlwaysBeRequired(string $serviceName): bool
    {
        return in_array($serviceName, [ 'email' ], true);
    }

    /**
     * If the field must always be enabled and still it's set as disabled, enable it.
     * If the field must always be required and still it's set as optional, make it required.
     *
     * @param AEntity $entity
     */
    protected function checkAvailabilityAndRequirement(AEntity $entity)
    {
        $serviceName = $entity->getServiceName();

        if ($this->shouldFieldAlwaysBeEnabled($serviceName) && !$entity->getEnabled()) {
            $entity->setEnabled(true);
        }

        if ($this->shouldFieldAlwaysBeRequired($serviceName) && !$entity->getRequired()) {
            $entity->setRequired(true);
        }
    }

    /**
     * Post-validate new entity
     *
     * @param AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);
        $this->checkAvailabilityAndRequirement($entity);

        if (in_array($entity->getServiceName(), $this->getForbiddenServiceNames())) {
            $result = false;
            $this->errorMessages[] = static::t(
                'The service name X is reserved and cannot be used for an address field.',
                [ 'value' => $entity->getServiceName() ]
            );
        }

        return $result;
    }
}
