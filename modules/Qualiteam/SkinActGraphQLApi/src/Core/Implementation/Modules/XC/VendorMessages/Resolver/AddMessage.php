<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\VendorMessages\Resolver;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use XLite\Model\Order;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper\ProductQuestion;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XLite\Model\Product;
use XC\VendorMessages\Model\Message;

/**
 * Class AddReview
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\ProductQuestions")
 *
 */

class AddMessage extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\AddMessage
{
    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return $this->createMessage(
            $context->getLoggedProfile(),
            $args
        );
    }

    /**
     * @param $profile
     * @param $params
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createMessage($profile, $params)
    {
        $result = false;

        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findOneByOrderNumber(
            $params['orderNumber']
        );

        if ($order) {
            $result = $order->buildNewMessage($profile, $params['message']);
            if ($result) {
                \XLite\Core\Database::getEM()->flush();
            }
        }

        return $result instanceof Message;
    }
}
