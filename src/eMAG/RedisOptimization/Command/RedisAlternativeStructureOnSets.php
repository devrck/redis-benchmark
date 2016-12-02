<?php

namespace eMAG\RedisOptimization\Command;

use PhpCollection\Set;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisAlternativeStructureOnSets extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:alternative:struct:set')
            ->setDescription('Analyzing a different structure for SETs using Redis LISTs and PHP SETs.')
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
        $pipe = $this->client->pipeline();
        $pipe->lpush($string, $items);
        list(,$totalItems)= $pipe->llen($string)->execute();
        $this->progressBar->advance($parts);
        $items = $pipe = null;
        $items = new Set();
        $batchSize = 10000;
        for ($i = 0; $i < $totalItems; $i+=$batchSize ) {
            $items->addAll($this->client->lrange($string, $i, $i+$batchSize));
        }
        $this->progressBar->advance($parts);
        $total = $items->count();

        for ($i = 0 ; $i < 100 ; $i++) {
            $items->contains(sprintf('%s_%d', $string, rand()));
        }
        $this->progressBar->advance($parts);

        $this->setFinishMessage(sprintf('<info>Hibrid solution for SETs took <comment>%fs</comment> and has <comment>%d</comment> elements in it</info>', (microtime(true) - $start), $total));
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'list:sets';
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
        return 'Started testing PHP SETs with Redis LISTs';
    }
}