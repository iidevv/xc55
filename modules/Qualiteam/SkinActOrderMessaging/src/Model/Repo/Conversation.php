<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * Autocomplete controller
 * @Extender\Mixin
 */
abstract class Conversation extends \XC\VendorMessages\Model\Repo\Conversation
{

    /**
     * Find users dialogue
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XC\VendorMessages\Model\Conversation|null
     */
    public function findGeneralDialogue($profile)
    {
        if ($profile) {
            $qb    = $this->createQueryBuilder();
            $alias = $this->getMainAlias($qb);

            $qb->andWhere("{$alias}.order IS NULL")
                ->andWhere("{$alias}.author = :profile")
                ->setParameter('profile', $profile);

            return $qb->getSingleResult();
        }

        return null;
    }

    /**
     * @param array                $ids
     * @param \XLite\Model\Profile $profile
     */
    public function markUnread(array $ids, $profile)
    {
        $readTable          = \XLite\Core\Database::getRepo('XC\VendorMessages\Model\MessageRead')->getTableName();
        $messagesTable      = \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')->getTableName();
        $conversationsTable = $this->getTableName();

        $selectQuery = "SELECT m.id FROM {$messagesTable} m INNER JOIN {$conversationsTable} c ON c.id = m.conversation_id AND c.id IN (:identifiers) WHERE m.profile_id != :profileId";
        $query       = "DELETE r FROM {$readTable} AS r WHERE r.message_id IN ($selectQuery) AND r.profile_id = :profileId";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId'   => $profile->getProfileId(),
            'identifiers' => $ids,
        ], [
            'profileId'   => \PDO::PARAM_INT,
            'identifiers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY,
        ]);
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    public function markUnreadAll($profile)
    {
        $readTable = \XLite\Core\Database::getRepo('XC\VendorMessages\Model\MessageRead')->getTableName();
        $messagesTable      = \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')->getTableName();

        $selectQuery = "SELECT m.id FROM {$messagesTable} m WHERE m.profile_id != :profileId";
        $query = "DELETE r FROM {$readTable} AS r WHERE r.message_id IN ($selectQuery) AND r.profile_id = :profileId";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId' => $profile->getProfileId(),
        ], [
            'profileId' => \PDO::PARAM_INT,
        ]);
    }

    public function createGeneralConversation($profile)
    {
        $conversation = new \XC\VendorMessages\Model\Conversation();
        $conversation->setOrder(null);
        $conversation->setAuthor($profile);
        $conversation->addMember($profile);
        $profile->setGeneralConversation($conversation);

        Database::getEM()->persist($conversation);
        Database::getEM()->flush();

        return $conversation;
    }

}