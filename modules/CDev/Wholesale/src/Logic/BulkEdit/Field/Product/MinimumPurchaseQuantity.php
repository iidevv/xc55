<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Logic\BulkEdit\Field\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class MinimumPurchaseQuantity extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => array_replace(
                $options,
                [
                    'label'             => $options['label'] ?? 0,
                    'type'              => 'XLite\View\FormModel\Type\PatternType',
                    'inputmask_pattern' => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'position'          => $options['position'] ?? 0,
                ]
            ),
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => 1,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $membershipRepo = \XLite\Core\Database::getRepo('XLite\Model\Membership');
        $repo = \XLite\Core\Database::getRepo('CDev\Wholesale\Model\MinQuantity');

        $membershipId = str_replace('membership_', '', $name);
        $membership = $membershipRepo->find($membershipId);

        $data = max(1, (int) $data->{$name});

        $minQuantity = $repo->getMinQuantity($object, $membership);
        if ($minQuantity) {
            $minQuantity->setQuantity($data);
        } else {
            $minQuantity = [
                'quantity' => $data,
                'product'  => $object,
            ];

            if ($membership) {
                $minQuantity['membership'] = $membership;
            }

            $repo->insertInBatch([$minQuantity]);
        }
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => $options['label'],
                'orderBy' => $options['position'] ?? 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        $membershipRepo = \XLite\Core\Database::getRepo('XLite\Model\Membership');

        $membershipId = str_replace('membership_', '', $name);
        $membership = $membershipRepo->find($membershipId);

        return $object->getMinQuantity($membership);
    }
}
