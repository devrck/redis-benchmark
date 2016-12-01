<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisScanKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:scan')
            ->setDescription('Will loop through a set of keys to ask for their existence.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $this->runCommand('benchmark:set:pipeline', ['--key' => $string,]);

        $nextCursor = 0;
        do {
            //@note: this is bullshit you have to know the prefix for use with MATCH!!!
            list($nextCursor, $itemsKeys) = $this->client->scan($nextCursor, ['MATCH' => sprintf('devck:%s*', $string),]);
            $this->progressBar->advance();
        } while ($nextCursor != 0);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rsk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started SCAN test';
    }
}