<?php
declare(strict_types=1);

namespace WapplerSystems\A21glossary\EventListener;


use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use WapplerSystems\A21glossary\Processor;

#[AsEventListener(identifier: 'a21glossary/content-modifier')]
final class AfterCacheableContentIsGeneratedEventListener
{
    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $request = $event->getRequest();
        $setup = $request->getAttribute('frontend.typoscript')?->getSetupArray() ?? [];
        $config = $setup['plugin.']['tx_a21glossary.']['settings.'] ?? [];

        $processor = GeneralUtility::makeInstance(Processor::class);
        $event->setContent($processor->main($event->getContent(), $request, $config));
    }
}
