<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

/**
 * Interface ProcessLoggableInterface.
 */
interface ProcessLoggableInterface
{
    /**
     * @return Process|null
     */
    public function getProcess(): ?Process;
}
