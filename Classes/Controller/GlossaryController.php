<?php

namespace WapplerSystems\A21glossary\Controller;

use GeorgRinger\NumberedPagination\NumberedPagination;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use WapplerSystems\A21glossary\Domain\Model\Glossary;
use WapplerSystems\A21glossary\Domain\Repository\GlossaryRepository;

class GlossaryController extends ActionController
{

    public function __construct(readonly GlossaryRepository $glossaryRepository)
    {

    }

    /**
     * @param string $char
     *
     * @throws InvalidQueryException
     */
    public function indexAction($char = null): ResponseInterface
    {
        $showAbbreviationsOnlyInGlossaryList = isset($this->settings['showAbbreviationsOnlyInGlossaryList'])
             ? (bool)$this->settings['showAbbreviationsOnlyInGlossaryList']
             : false;
        
        if (!empty($char)) {
            $glossaryItems = $this->glossaryRepository->findAllWithChar($char, $showAbbreviationsOnlyInGlossaryList);
        } else {
            $glossaryItems = $this->glossaryRepository->findAllFiltered($showAbbreviationsOnlyInGlossaryList);
        }

        $paginationConfiguration = $this->settings['paginate'] ?? [];
        $itemsPerPage = (int)($paginationConfiguration['itemsPerPage'] ?: 10);
        $maximumNumberOfLinks = (int)($paginationConfiguration['maximumNumberOfLinks'] ?? 0);

        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $glossaryItems, $currentPage, $itemsPerPage);
        $paginationClass = $paginationConfiguration['class'] ?? SimplePagination::class;
        if ($paginationClass === NumberedPagination::class && $maximumNumberOfLinks && class_exists(NumberedPagination::class)) {
            $pagination = GeneralUtility::makeInstance(NumberedPagination::class, $paginator, $maximumNumberOfLinks);
        } elseif (class_exists($paginationClass)) {
            $pagination = GeneralUtility::makeInstance($paginationClass, $paginator);
        } else {
            $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);
        }

        $this->view->assign('index', $this->glossaryRepository->findAllForIndex($showAbbreviationsOnlyInGlossaryList));
        $this->view->assign('currentChar', $char);
        $this->view->assign('pagination', ['pagination' => $pagination, 'paginator' => $paginator]);

        return $this->htmlResponse();
    }

    /**
     * @param string $q
     *
     * @throws InvalidQueryException
     */
    public function searchAction($q): ResponseInterface
    {
        $showAbbreviationsOnlyInGlossaryList = isset($this->settings['showAbbreviationsOnlyInGlossaryList'])
             ? (bool)$this->settings['showAbbreviationsOnlyInGlossaryList']
             : false;

        $this->view->assign('q', $q);
        $this->view->assign('items', $this->glossaryRepository->findAllWithQuery($q, $showAbbreviationsOnlyInGlossaryList));

        return $this->htmlResponse();
    }

    /**
     * @param Glossary $entry
     * @return void
     */
    public function showAction(Glossary $entry): ResponseInterface
    {

        $this->view->assign('item', $entry);

        return $this->htmlResponse();
    }
}
