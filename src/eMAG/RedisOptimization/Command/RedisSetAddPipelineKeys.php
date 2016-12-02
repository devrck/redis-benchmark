<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisSetAddPipelineKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:pipeline:sadd')
            ->setDescription('Adds into a set a number of members using pipelines.')
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
            $pipe->sadd('redis:sadd', sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }
        $pipe->execute();
        $this->setFinishMessage(sprintf('<info>SADD with pipeline of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
        $pipe = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rsapk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started SADD with pipeline test';
    }
}