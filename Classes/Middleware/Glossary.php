<?php

namespace WapplerSystems\A21glossary\Middleware;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Ronny Vorpahl (vorpahl@artplan21.de)
 *  (c) 2003 Andreas Schwarzkopf (schwarzkopf@artplan21 de)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use WapplerSystems\A21glossary\Processor;

/**
 *
 * @author    Sven Wappler <typo3YYYY@wappler.systems>
 */
class Glossary implements MiddlewareInterface
{


    /**
     * Calls the "unavailableAction" of the error controller if the system is in maintenance mode.
     * This only applies if the REMOTE_ADDR does not match the devIpMask
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $response = $handler->handle($request);
        if (
            !($response instanceof NullResponse)
            && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController
        ) {
            $body = $response->getBody();
            $body->rewind();
            $contents = $response->getBody()->getContents();


            $tsConfig = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(ConfigurationManagerInterface::class)
                ->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
                );

            $config = $tsConfig['plugin.']['tx_a21glossary.']['settings.'] ?? [];
            $config = GeneralUtility::removeDotsFromTS($config);

            $processor = GeneralUtility::makeInstance(Processor::class);
            $content = $processor->main($contents,$config);


            $body = new Stream('php://temp', 'rw');
            $body->write($content);
            $response = $response->withBody($body);
        }

        return $response;
    }


}
