<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Category view model
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $schema = [];
        $addition = $this->getAdditionalSections();
        $flagAdded = false;

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ($name === 'description') {
                $schema['flyoutColumns'] = $addition['flyoutColumns'];
                $flagAdded = true;
            }
        }

        if (!$flagAdded) {
            $schema += $addition;
        }

        $this->schemaDefault = $schema;
    }

    /**
     *
     * @return array
     */
    protected function getAdditionalSections()
    {
        return [
            'flyoutColumns' => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
                self::SCHEMA_LABEL    => 'Number of columns for subcategories flyout menu',
                self::SCHEMA_HELP     => 'If "Use multicolumn layout for subcategories" option selected. Set 0 to use default value from module settings',
                self::SCHEMA_REQUIRED => false,
                \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
            ],
        ];
    }

    /**
     * Public for protected cleanDTOsCache()
     *
     * @return  void
     */
    public function publicCleanDTOsCache()
    {
        $this->cleanDTOsCache();
    }
}
