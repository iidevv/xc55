<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\VendorMessages\Resolver;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XC\VendorMessages\Model\Message;



use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\VendorMessages")
 *
 */

class Messages extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Messages
{
    /**
     * @param                                    $val
     * @param                                    $args
     * @param XCartContext                       $context
     * @param ResolveInfo                        $info
     *
     * @return array|mixed
     * @throws \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findOneByOrderNumber(
            $args['orderNumber']
        );

        if ($order) {
            $messages = $order->getMessages();
        }

        if ($messages instanceof Collection) {
            $messages = $messages->toArray();
        }

        return array_map(
            function($message) {
                return $this->mapToDto($message);
            },
            $messages
        );
    }

    /**
     * @param Message $model
     * @return array
     */
    protected function mapToDto(Message $model)
    {
        return [
            'id'        => $model->getId(),
            'title'     => $model->getBody(),
            'date_time' => \XLite\Core\Converter::formatTime($model->getDate())
        ];
    }
}
