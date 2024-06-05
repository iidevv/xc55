<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\API\Endpoint\Order\Transformer;

use DateTimeImmutable;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use QSL\AbandonedCartReminder\API\Endpoint\Order\DTO\Output as ModuleOutputDTO;
use QSL\AbandonedCartReminder\API\Endpoint\Order\Transformer\Email\OutputTransformerInterface;
use QSL\AbandonedCartReminder\Model\Order;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends \XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract
{
    protected OutputTransformerInterface $emailTransformer;

    /**
     * @required
     */
    public function setEmailTransformer(OutputTransformerInterface $emailTransformer): void
    {
        $this->emailTransformer = $emailTransformer;
    }

    /**
     * @param Order $object
     */
    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::basicTransform($dto, $object, $to, $context);

        $dto->recovered = $object->getRecovered();
        $dto->cart_reminders_sent = $object->getCartRemindersSent();
        $dto->cart_reminder_date = $object->getCartReminderDate() ? new DateTimeImmutable('@' . $object->getCartReminderDate()) : null;
        $dto->lost = $object->getLost();
        $dto->last_visit_date = $object->getLastVisitDate() ? new DateTimeImmutable('@' . $object->getLastVisitDate()) : null;
        $dto->cart_recovery_email = $object->getCartRecoveryEmail() ? $this->emailTransformer->transform($object->getCartRecoveryEmail(), $to, $context) : null;

        $dto->cart_reminder_emails = [];
        foreach ($object->getCartReminderEmails() as $email) {
            $dto->cart_reminder_emails[] = $this->emailTransformer->transform($email, $to, $context);
        }

        return $dto;
    }
}
