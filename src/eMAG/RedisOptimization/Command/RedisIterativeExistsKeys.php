<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisIterativeExistsKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:exists:iterative')
            ->setDescription('Will loop through a set of keys to ask for their existence one by one.')
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
        for ($i = 0; $i < $this->totalItems; $i++) {
            $this->client->exists(sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }

        $this->setFinishMessage(sprintf('<info>Iterative EXISTS of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'riek';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started iterative EXISTS test';
    }

}