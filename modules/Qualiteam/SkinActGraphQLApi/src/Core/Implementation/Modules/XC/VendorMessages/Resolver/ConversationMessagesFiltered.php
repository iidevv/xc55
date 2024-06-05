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

class ConversationMessagesFiltered implements ResolverInterface
{
    protected $isAdmin;
    protected $adminMessages;
    protected $customerMessages;

    public function __construct($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

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

        foreach ($messages as $key => $message) {
            if ($this->isAdmin === $this->isAuthorAdmin($message)) {
                $this->adminMessages[$key] = $message;
            }

            if (!$this->isAdmin === $this->isAuthorUser($message)) {
                $this->customerMessages[$key] = $message;
            }
        }

        if ($this->adminMessages) {
            $messages = $this->adminMessages;
        } else {
            $messages = $this->customerMessages;
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
     */
    protected function isAuthorAdmin(Message $message)
    {
        return $message->getAuthor()->isAdmin();
    }

    /**
     * @param Message $message
     */
    protected function isAuthorUser(Message $message)
    {
        return !$message->getAuthor()->isAdmin();
    }

    /**
     * @param Message $model
     * @return array
     */
    protected function mapToDto(Message $model, Profile $profile)
    {
        return [
            'id'        => $model->getId(),
            'title'     => $model->getBody(),
            'date_time' => \XLite\Core\Converter::formatTime($model->getDate()),
            'author'    => $model->getAuthorName(),
            'read'      => $model->isRead($profile)
        ];
    }
}
