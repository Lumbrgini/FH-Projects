<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);

    $containerBuilder->addDefinitions(
        [
            Twig::class => function (ContainerInterface $c){
                $settings = $c->get("settings");
                $viewSettings = $settings["view"];

                return Twig::create(
                    $viewSettings['templates'],
                    [
                        'cache'=>$viewSettings['cache'],
                        'auto_reload'=>$viewSettings['auto_reload']
                    ]
                );
            }
        ]
    );
};
