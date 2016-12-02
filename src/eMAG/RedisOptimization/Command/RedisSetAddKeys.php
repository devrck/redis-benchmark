<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisSetAddKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:sadd')
            ->setDescription('Adds into a set a number of members.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $start = microtime(true);
        $items = [];
        for ($i = 0; $i < $this->totalItems; $i++) {
            $items[] = sprintf('%s%d', $string, $i);
            $this->progressBar->advance();
        }
        $this->client->sadd($string, $items);
        $this->setFinishMessage(sprintf('<info>SADD with pipeline of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
        $items = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'redis:sadd';
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
        return 'Started SADD test (taking advantage of SADD can accept an array of members)';
    }
}