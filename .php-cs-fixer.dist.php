<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('Kernel.php')
    ->notPath('bootstrap.php')
    ->in(__DIR__.'/{core,src,tests}')
;

$header =<<<'HEADER'
This file is part of itk-dev/kontrolgruppen.

(c) 2019–2021 ITK Development

This source file is subject to the MIT license.
HEADER;
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => ['header' => $header],
        'list_syntax' => ['syntax' => 'short'],
        'no_superfluous_phpdoc_tags' => false,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'strict_comparison' => true,
        'phpdoc_align' => false,
    ])
    ->setFinder($finder)
;
