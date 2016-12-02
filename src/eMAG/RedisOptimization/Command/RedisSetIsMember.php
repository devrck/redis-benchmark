<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisSetIsMember extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:sismember')
            ->setDescription('Checks if a item belongs to a SET.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $this->runCommand('benchmark:sadd', ['--key' => $string]);

        $start = microtime(true);
        for ($i = 0; $i < $this->totalItems; $i++) {
            $this->client->sismember($string, sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }
        $this->setFinishMessage(sprintf('<info>SISMEMBER of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
        $items = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'redis:sismember';
    }

    /**
     * @return  bool
     */
    protected function isMultiKey()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started SISMEMBER iterative test';
    }
}