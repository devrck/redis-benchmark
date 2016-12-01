<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisPipelineSettingKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:set:pipeline')
            ->setDescription('In a loop will set a key in a pipeline and then execute it against redis server.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $start = microtime(true);
        $pipe = $this->client->pipeline();
        for ($i = 0; $i < $this->totalItems; $i++) {
            $pipe->set(sprintf('%s%d', $string, $i), $i);
            $this->progressBar->advance();
        }
        $pipe->execute();
        $this->setFinishMessage(sprintf('<info>Pipeline SET of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
        $pipe = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rpsk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started pipeline SET test';
    }
}