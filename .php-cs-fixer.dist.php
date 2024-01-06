<?php
require_once __DIR__ . '/vendor/litonarefin/wp-php-cs-fixer/loader.php';

$finder = PhpCsFixer\Finder::create()
	->exclude( 'node_modules' )
	->exclude( 'vendors' )
	->exclude( 'assets' )
	->in( __DIR__ );

$config = new PhpCsFixer\Config();
$config
	->registerCustomFixers(
		array(
			new \Fixer\SpaceInsideParenthesisFixer(),
			new \Fixer\BlankLineAfterClassOpeningFixer(),
		)
	)
	->setRiskyAllowed( true )
	->setUsingCache( false )
	->setRules( \Fixer\Fixer::rules() )
	->setFinder( $finder );

return $config;
