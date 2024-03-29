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
 * Class EconomyEntryEnumType.
 */
final class EconomyEntryEnumType extends AbstractEnumType
{
    public const SERVICE = 'SERVICE';
    public const ACCOUNT = 'ACCOUNT';
    public const INCOME = 'INCOME';

    public const TRANSLATIONS = [
        self::SERVICE => 'common.enum.economy_entry.service',
        self::ACCOUNT => 'common.enum.economy_entry.account',
        self::INCOME => 'common.enum.economy_entry.income',
    ];

    protected static array $choices = self::TRANSLATIONS;
}
