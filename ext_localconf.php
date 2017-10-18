<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


$extConf = unserialize($_EXTCONF);

// select method of using glossary
switch(strtoupper($extConf['glossaryWHEN'])) {

	// fast: this hook is called with the cached page containing int-script elements for uncached subparts
	case 'BEFORECACHING':
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_a21glossary'] = 'EXT:a21glossary/class.tx_a21glossary.php:&tx_a21glossary->processHook';
		break;

	// slow: this one is for the final page containing also the uncached areas
	case 'AFTERCACHING':
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['tx_a21glossary'] = 'EXT:a21glossary/class.tx_a21glossary.php:&tx_a21glossary->processHook';
		break;

	// off: do not invoke the glossary engine automatically
	default:
	case 'OFF';
		break;

}

unset($extConf);

