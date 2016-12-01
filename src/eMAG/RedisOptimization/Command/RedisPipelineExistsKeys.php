<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisPipelineExistsKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:exists:pipeline')
            ->setDescription('Will loop through a set of keys to ask for their existence all at once.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $this->runCommand('benchmark:set:pipeline', ['--key' => $string,]);

        $start = microtime(true);
        $pipe = $this->client->pipeline();
        for ($i = 0; $i < $this->totalItems; $i++) {
            $pipe->exists(sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }

        $this->setFinishMessage(sprintf('<info>Pipeline EXISTS of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rpek';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started pipeline EXISTS test';
    }
}