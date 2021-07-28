<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class ProcessLogEntryLevelEnumType.
 */
class ProcessLogEntryLevelEnumType extends AbstractEnumType
{
    public const INFO = 'INFO';
    public const NOTICE = 'NOTICE';

    protected static $choices = [
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
    ];
}
