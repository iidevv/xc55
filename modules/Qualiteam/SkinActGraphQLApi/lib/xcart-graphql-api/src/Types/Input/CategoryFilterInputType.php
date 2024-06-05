<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class CategoryFilterInputType extends InputObjectType
{
    use Types\Traits\CollectionItemFilterTrait;

    public function configure()
    {
        return [
            'name'   => 'category_filter_input',
            'description' => 'Category filter input',
            'fields' => $this->defineFields()
        ];
    }

    protected function defineFields()
    {
        return $this->defineCommonFields();
    }

    public function __construct()
    {
        $config = $this->configure();

        parent::__construct($config);
    }
}
