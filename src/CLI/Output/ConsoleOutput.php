<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Output;

use League\Geotools\CLI\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ConsoleOutput extends \Symfony\Component\Console\Output\ConsoleOutput
{
    /**
     * {@inheritdoc}
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        parent::__construct($verbosity, $decorated, new OutputFormatter);
    }
}
