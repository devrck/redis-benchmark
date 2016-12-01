<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisIterativeSettingKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:set:iterative')
            ->setDescription('In a loop will set a key in redis server and then move on.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $start = microtime(true);
        for ($i = 0; $i < $this->totalItems; $i++) {
            $this->client->set(sprintf('%s%d', $string, $i), $i);
            $this->progressBar->advance();
        }
        $this->setFinishMessage(sprintf('<info>Iterative SET of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'risk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started iterative SET test';
    }
}