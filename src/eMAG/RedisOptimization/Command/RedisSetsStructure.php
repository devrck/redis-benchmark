<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisSetsStructure extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:struct:set')
            ->setDescription('Analyzing Redis SETs use-case.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $parts = ceil($this->totalItems/6);
        $string = $input->getOption('key');
        $start = microtime(true);
        $items = [];
        for ($i = 0; $i < $this->totalItems; $i++) {
            $items[] = sprintf('%s_%d', $string, $i);
        }
        $this->progressBar->advance($parts);
        $this->client->sadd($string, $items);
        $this->progressBar->advance($parts);
        $items = null;
        $items = $this->client->smembers($string);
        $this->progressBar->advance($parts);
        $total = count($items);

        $this->progressBar->advance($parts);
        $pipe = $this->client->pipeline();
        for ($i = 0 ; $i < 100 ; $i++) {
            $pipe->sismember($string, sprintf('%s_%d',$string, rand()));
        }
        $this->progressBar->advance($parts);
        $pipe->execute();
        $this->progressBar->advance($parts);

        $this->setFinishMessage(sprintf('<info>Redis SETs use-case took <comment>%fs</comment> and has <comment>%d</comment> elements in it</info>', (microtime(true) - $start), $total));
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'struct:sets';
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
        return 'Started testing Redis SETs';
    }

}