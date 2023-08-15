<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\MockBundle\CPR;

use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use Kontrolgruppen\CoreBundle\CPR\AbstractCprService;
use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\CPR\CprServiceResultInterface;
use Kontrolgruppen\CoreBundle\CPR\ServiceplatformenCprServiceResult;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ServiceplatformenCprService.
 */
class ServiceplatformenCprService extends AbstractCprService implements CprServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        $data = Yaml::parseFile(__DIR__.'/ServiceplatformenCprService/data.yaml');
        if (isset($data[(string)$cpr])) {
            return new ServiceplatformenCprServiceResult(json_decode(json_encode($data[(string)$cpr])));
        }

        throw new CprException(sprintf('Invalid CPR: %s', $cpr));
    }
}
