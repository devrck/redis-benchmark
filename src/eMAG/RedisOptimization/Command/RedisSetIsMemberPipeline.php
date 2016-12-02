<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisSetIsMemberPipeline extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:pipeline:sismember')
            ->setDescription('Checks if a batch of items belong to a SET using pipelines.')
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
        $pipe = $this->client->pipeline();
        for ($i = 0; $i < $this->totalItems; $i++) {
            $pipe->sismember($string, sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }
        $pipe->execute();

        $this->setFinishMessage(sprintf('<info>SISMEMBER pipeline of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, (microtime(true) - $start)));
        $pipe = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'redis:pipe:sismember';
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
        return 'Started SISMEMBER pipeline test';
    }
}