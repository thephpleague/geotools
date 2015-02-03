<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class OutputFormatter extends \Symfony\Component\Console\Formatter\OutputFormatter
{
    /**
     * {@inheritdoc}
     */
    public function __construct($decorated = false, array $styles = array())
    {
        $this->setStyle('label', new OutputFormatterStyle('yellow', 'black'));
        $this->setStyle('value', new OutputFormatterStyle('green', 'black'));

        parent::__construct($decorated, $styles);
    }
}
