<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;

class Tooltips implements ResolverInterface
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        throw new NoModule();
    }

    protected function prepareTooltipsResult(array $fields)
    {
        $tooltips = [];

        foreach ($fields as $name => $field) {

            $tooltips[] = [
                'name'    => $name,
                'title'   => $field['label'],
                'tooltip' => $field['tooltip'],
            ];
        }

        return $tooltips;
    }

    protected function getPreparedTooltipFields($data)
    {
        $prepared = [];
        foreach ($data as $section) {
            $prepared = array_merge($prepared, $this->getTooltipSectionFields($section));
        }
        return $prepared;
    }

    protected function getTooltipSectionFields($section)
    {
        $fields = [];
        foreach ($section as $name => $field) {
            if (isset($field['fields'])) {
                $fields = array_merge($fields, $this->getTooltipSectionFields($field['fields']));
            }

            $fields[$name] = [
                'label'   => $field['label'] ?? '',
                'tooltip' => $field['help'] ?? '',
            ];
        }
        return $fields;
    }
}