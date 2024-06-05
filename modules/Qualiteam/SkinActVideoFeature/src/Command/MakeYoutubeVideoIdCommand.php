<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\Core\Database;

class MakeYoutubeVideoIdCommand extends Command
{
    const CHUNK_LENGTH = 20;

    protected static $defaultName = 'SkinActVideoFeature:MakeYoutubeVideoId';

    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;

        foreach (Database::getRepo('Qualiteam\SkinActVideoFeature\Model\EducationalVideo')->findFrame($position, $length) as $video) {

            if ($video->getVideoCode() && !$video->getYoutubeVideoId()) {
                $code = $video->getVideoCode();
                preg_match( '/src="([^"]*)"/i', $code, $link);

                $link = !empty($link) && !empty($link[1]) ? $link[1] : $code;

                preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $link, $id);

                if (!empty($id) && !empty($id[1])) {
                    $video->setYoutubeVideoId($id[1]);
                }
            }

            $processed++;
        }

        if (0 < $processed) {
            Database::getEM()->flush();
        }

        return $processed;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 0;

        do {

            $processed = $this->updateChunk($i, static::CHUNK_LENGTH);

            if (0 < $processed) {
                Database::getEM()->clear();
            }

            $i += $processed;

            if ($processed > 0) {
                $output->writeln('Processed: ' . $i . ' videos');
            }

        } while (0 < $processed);

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}