<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\VendorMessages\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XC\VendorMessages\Model\Repo\Conversation as ConversationRepo;



use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\VendorMessages")
 *
 */

class Conversations extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Conversations
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
        $profile = $this->getProfile($val, $context) ;

        return array_map(static function (\XC\VendorMessages\Model\Conversation $item) use ($profile) {

            return static::mapToDto($item, $profile);

        }, $this->getConversations($profile));
    }

    /**
     * @param Profile $profile
     * @return array
     */
    protected function getConversations(Profile $profile)
    {
        $condition = new \XLite\Core\CommonCell();

        $condition->{ConversationRepo::P_MEMBER} = $profile;

        $condition->{ConversationRepo::P_ORDER_BY} = [
            'read_messages',
            'asc'
        ];

        //$condition->{ConversationRepo::P_ORDERS_CONDITIONS} = true;

        return \XLite\Core\Database::getRepo('\XC\VendorMessages\Model\Conversation')->search(
            $condition
        );
    }

    /**
     * @param \XC\VendorMessages\Model\Conversation $conversation
     * @return array
     */
    public static function mapToDto(\XC\VendorMessages\Model\Conversation $conversation, $profile)
    {
        return [
            'conversation'  => $conversation,
            'profile'       => $profile,
            'id'            => $conversation->getId(),
            'order_id'      => $conversation->getOrder() ? $conversation->getOrder()->getOrderId() : '',
            'order_number'  => $conversation->getOrder() ? $conversation->getOrder()->getOrderNumber() : '',
            'unreadCount'   => $conversation->countUnreadMessages($profile),
            'messages'      => new ConversationMessages(),
            'messages_admin' => new ConversationMessagesFiltered(true),
            'messages_user' => new ConversationMessagesFiltered(false),
        ];
    }

    /**
     * @param $val
     * @param $args
     */
    protected function getProfile($val, $context)
    {
        if (isset($val['id'])) {
            return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($val['id']);
        }

        return $context->getLoggedProfile();
    }
}
