<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\API\Endpoint\Order\Transformer\Email;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use QSL\AbandonedCartReminder\API\Endpoint\Order\DTO\Email\OrderAbandonedCartReminderEmailOutput as OutputDTO;
use QSL\AbandonedCartReminder\Model\Email;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Email $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getEmailId();
        $dto->date_sent = $object->getDateSent() ? new DateTimeImmutable('@' . $object->getDateSent()) : null;
        $dto->date_clicked = $object->getDateClicked() ? new DateTimeImmutable('@' . $object->getDateClicked()) : null;
        $dto->date_placed = $object->getDatePlaced() ? new DateTimeImmutable('@' . $object->getDatePlaced()) : null;
        $dto->date_paid = $object->getDatePaid() ? new DateTimeImmutable('@' . $object->getDatePaid()) : null;
        $dto->reminder_id = $object->getReminderId();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Email;
    }
}
