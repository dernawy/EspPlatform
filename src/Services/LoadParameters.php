<?php

/**
 * This service is for autoload the env variables in "config.json" file
 */

namespace App\Services;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\EnvVarLoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LoadParameters implements EnvVarLoaderInterface
{

    public $params;
    public function __construct(ContainerBagInterface $params){
        $this->params = $params;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loadEnvVars(): array
    {

        $fileName = $this->params->get('config_file_path');

        if (!is_file($fileName)) {
            // throw an exception or just ignore this loader, depending on your needs
        }

        return json_decode(file_get_contents($fileName), true);
    }
}