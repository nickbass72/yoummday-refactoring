<?php

namespace App;

use App\Provider\InMemoryTokenProvider;
use App\Provider\TokenProviderInterface;
use App\Serializer\TokenSerializer;
use Progphil1337\Config\Config;
use ProgPhil1337\DependencyInjection\ClassLookup;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\App;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\DefaultRequestPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\RoutingPipelineHandler;

class Application
{
    public const PIPELINE_HANDLERS = [
        RoutingPipelineHandler::class,
        DefaultRequestPipelineHandler::class,
    ];

    private readonly App $application;

    /**
     * @param string[]|string $configFiles
     */
    public function __construct(array|string $configFiles)
    {
        $config = Config::create($configFiles);

        $container = new Injector(new ClassLookup());
        $container->getLookup()
            ->singleton($config)
            ->register($config)
            ->singleton($container)
            ->register($container)
        ;

        $this->buildDependencies($container->getLookup());

        $this->application = new App($config, $container);
    }

    public function run(): int
    {
        return $this->application->run(self::PIPELINE_HANDLERS);
    }

    private function buildDependencies(ClassLookup $classLookup): void
    {
        $classLookup
            ->singleton(TokenSerializer::class)
            ->singleton(InMemoryTokenProvider::class)
            ->alias(TokenProviderInterface::class, InMemoryTokenProvider::class)
        ;
    }
}
