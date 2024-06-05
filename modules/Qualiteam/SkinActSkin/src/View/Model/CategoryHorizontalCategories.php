<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Category;

/**
 * Category view model
 * @Extender\Mixin
 * @Extender\Depend("QSL\HorizontalCategoriesMenu")
 */
class CategoryHorizontalCategories extends \XLite\View\Model\Category
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

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ($name === 'flyoutColumns' && $this->isRootCategory()) {
                $schema[$name][\XLite\View\FormField\Input\Text\Integer::PARAM_READ_ONLY] = true;
            }
        }

        $this->schemaDefault = $schema;
    }

    public function isRootCategory()
    {
        $model = $this->getModelObject();
        $parentId = $model->getParent() ? $model->getParent()->getCategoryId() : null;
        return Database::getRepo(Category::class)->getRootCategoryId() === $parentId;
    }

}
