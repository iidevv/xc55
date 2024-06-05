<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\VendorMessages\Resolver;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XC\VendorMessages\Model\Conversation;
use XC\VendorMessages\Model\Message;

/**
 *
 * @Decorator\Depend("XC\VendorMessages")
 */

class ConversationMessages implements ResolverInterface
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
        $profile = $val['profile'];

        $conversation = $val['conversation'];

        $messages = [];

        /** @var Conversation $conversation */
        if ($conversation) {
            $messages = $conversation->getMessages();
        }

        if ($messages instanceof Collection) {
            $messages = $messages->toArray();
        }

        return array_map(
            function($message) use ($profile) {
                return $this->mapToDto($message, $profile);
            },
            $messages
        );
    }

    /**
     * @param Message $message
     * @param Profile $profile
     *
     * @return bool
     */
    protected function byUser(Message $message, Profile $profile)
    {
        return $message->getAuthor() === $profile;
    }

    /**
     * @param Message $model
     * @param Profile $profile
     *
     * @return array
     */
    protected function mapToDto(Message $model, Profile $profile)
    {
        return [
            'id'        => $model->getId(),
            'title'     => $model->getBody(),
            'date_time' => \XLite\Core\Converter::formatTime($model->getDate()),
            'author'    => $model->getAuthorName(),
            'byUser'    => $this->byUser($model, $profile),
            'read'      => $model->isRead($profile)
        ];
    }
}
