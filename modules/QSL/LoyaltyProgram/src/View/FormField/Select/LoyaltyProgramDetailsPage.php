<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\FormField\Select;

/**
 * Form field to choose which page to use as the Loyalty Program Details page.
 */
class LoyaltyProgramDetailsPage extends \XLite\View\FormField\Select\Regular
{
    /**
     * Set value.
     *
     * @param mixed $value Value to set
     */
    public function setValue($value)
    {
        if (is_null($value)) {
            $value = 0;
        }

        parent::setValue($value);
    }

    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [
            0 => static::t('--- Built-in loyalty program page template ---'),
        ];

        if ($this->isSimpleCMSEnabled()) {
            foreach ($this->getStaticPages() as $page) {
                $list[$page->getId()] = htmlspecialchars($page->getName());
            }
        }

        return $list;
    }

    /**
     * Get all SimpleCMS pages.
     *
     * @return mixed
     */
    protected function getStaticPages()
    {
        return \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->findAll();
    }

    /**
     * Check whether SimpleCMS module is enabled.
     *
     * @return boolean
     */
    protected function isSimpleCMSEnabled()
    {
        return \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev', 'SimpleCMS');
    }
}
