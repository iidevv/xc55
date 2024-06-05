<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command;

use Qualiteam\SkinActYotpoReviews\Core\Dispatcher\CreateProductVariantDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateProductVariant extends Command
{
    protected static $defaultName = 'SkinActYotpoReview:CreateProductVariant';

    protected MessageBusInterface            $bus;
    protected CreateProductVariantDispatcher $dispatcher;

    public function __construct(MessageBusInterface $bus, CreateProductVariantDispatcher $dispatcher)
    {
        parent::__construct();
        $this->bus        = $bus;
        $this->dispatcher = $dispatcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $this->dispatcher->getMessage();
        $this->bus->dispatch($message);

        return Command::SUCCESS;
    }
}