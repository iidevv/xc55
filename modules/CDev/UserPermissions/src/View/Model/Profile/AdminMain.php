<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionAccess()
    {
        $persistentModel = $this->getModelObject() && $this->getModelObject()->isPersistent();
        if (!$persistentModel && isset($this->accessSchema['roles'])) {
            $this->accessSchema['roles'][self::SCHEMA_COMMENT] = static::t(
                'Attention! You are creating an account with full access. Roles warning',
                [
                    'roles_link' => $this->buildURL('roles'),
                    'kb_link'    => static::t('https://support.x-cart.com/en/articles/4575931-user-roles'),
                ]
            );
        }

        return parent::getFormFieldsForSectionAccess();
    }
}
