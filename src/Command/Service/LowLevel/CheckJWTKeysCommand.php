<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Command\Service\LowLevel;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckJWTKeysCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'xcart:ll:check-jwt-keys';

    private JWTEncoderInterface $encoder;

    public function __construct(
        JWTEncoderInterface $encoder
    ) {
        parent::__construct();

        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this->setHidden(true);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->encoder->encode([]);
        } catch (\Exception $e) {
            $output->write('fail');
        }

        return Command::SUCCESS;
    }
}
