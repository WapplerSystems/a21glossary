<?php
declare(strict_types=1);

namespace WapplerSystems\A21glossary\EventListener;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use WapplerSystems\A21glossary\Processor;

final class AfterCacheableContentIsGeneratedEventListener
{
    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {

        $content = $event->getController()->content;


        $tsConfig = GeneralUtility::makeInstance(ConfigurationManagerInterface::class)
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );

        $config = $tsConfig['plugin.']['tx_a21glossary.']['settings.'] ?? [];

        $processor = GeneralUtility::makeInstance(Processor::class);
        $event->getController()->content = $processor->main($content, $config);


    }
}
