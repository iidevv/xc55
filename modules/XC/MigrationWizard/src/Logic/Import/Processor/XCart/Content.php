<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Content processor
 */
class Content extends \XLite\Logic\Import\Processor\AProcessor
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePreProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Languages',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ContactUs',
        ];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Pages',
            // goes after pages since there can be links to pages
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Menu',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Sitemap',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\XMLSitemap',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return null;
    }

    // }}} </editor-fold>
}
