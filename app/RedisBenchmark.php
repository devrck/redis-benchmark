<?php

use eMAG\RedisOptimization\Command as RedisCmd;
use Predis\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

class RedisBenchmark extends Application implements LoggerAwareInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $parameters;

    public function __construct($name, $version)
    {
        parent::__construct($name, $version);
        $this->loadConfiguration();
        $this->boot();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return  $this
     */
    public function registerExtraCommands()
    {
        /** @var RedisCmd\AbstractRedisCommand $redisCommand */
        foreach ($this->getRedisCommands() as $redisCommand) {
            $this->add($redisCommand->setLogger($this->logger));
        }

        return $this;
    }

    /**
     * @param   Client  $client
     *
     * @return  $this
     *
     * @throws  \Exception
     */
    public function setRedisClient(Client $client)
    {
        $this->client = $client;
        $this->client->connect();
        if (!$client->isConnected()) {
            throw new \Exception("Can't connect to redis server configuration!");
        }

        return $this;
    }

    /**
     * @return  Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return  array
     */
    public function getRedisCommands()
    {
        //@todo: use symfony Finder and create this dynamic
        return [
            new RedisCmd\RedisCleanCommand(),

            new RedisCmd\RedisIterativeSettingKeys(),
            new RedisCmd\RedisIterativeGettingKeys(),

            new RedisCmd\RedisPipelineSettingKeys(),
            new RedisCmd\RedisPipelineGettingKeys(),

            new RedisCmd\RedisIterativeExistsKeys(),
            new RedisCmd\RedisPipelineExistsKeys(),

            new RedisCmd\RedisScanKeys(),
        ];
    }

    /**
     * @return  void
     */
    public function boot()
    {
        $this
            ->setRedisClient(new Predis\Client($this->getParameter('redis.client.[connection]'), $this->getParameter('redis.client.[options]')))
            ->setLogger(new \Monolog\Logger($this->getParameter('logger.name')))
            ->registerExtraCommands()
        ;
    }

    /**
     * @return  $this
     */
    public function loadConfiguration ()
    {
        $this->parameters = json_decode(file_get_contents(sprintf('%s/config/config.json', __DIR__)), true);

        $translatedConfigs = [];
        $this->buildAlias($this->parameters, $translatedConfigs);
        $this->parameters = $translatedConfigs;

        return $this;
    }

    /**
     * @param   string  $name
     *
     * @return  mixed
     */
    public function getParameter($name)
    {
        return isset($this->parameters[$name])
            ? $this->parameters[$name]
            : null
        ;
    }

    /**
     * @param   string  $name
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param   array   $array
     * @param   array   $storage
     * @param   string  $parentKey
     *
     * @return  void
     */
    private function buildAlias (array $array, array &$storage, $parentKey = '')
    {
        foreach ($array as $key => $value) {
            $itemKey = (($parentKey)? $parentKey . '.' : '') . $key;
            if (is_array($value) && strpos($key, '[') !== 0) {
                $this->buildAlias($value, $storage, $itemKey);
            } else {
                $storage[$itemKey] = $value;
            }
        }
    }
}