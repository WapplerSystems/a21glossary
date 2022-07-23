<?php

namespace WapplerSystems\A21glossary\Controller;

use GeorgRinger\NumberedPagination\NumberedPagination;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use WapplerSystems\A21glossary\Domain\Model\Glossary;
use WapplerSystems\A21glossary\Domain\Repository\GlossaryRepository;

class GlossaryController extends ActionController
{
    /**
     * @var \WapplerSystems\A21glossary\Domain\Repository\GlossaryRepository
     */
    protected $glossaryRepository;

    public function injectGlossaryRepository(GlossaryRepository $glossaryRepository)
    {
        $this->glossaryRepository = $glossaryRepository;
    }

    /**
     * @param string $char
     *
     * @throws InvalidQueryException
     */
    public function indexAction($char = null)
    {
        if (!empty($char)) {
            $glossaryItems = $this->glossaryRepository->findAllWithChar($char);
        } else {
            $glossaryItems = $this->glossaryRepository->findAll();
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

        $this->view->assign('index', $this->glossaryRepository->findAllForIndex());
        $this->view->assign('currentChar', $char);
        $this->view->assign('pagination', ['pagination' => $pagination, 'paginator' => $paginator]);
    }

    /**
     * @param string $q
     *
     * @throws InvalidQueryException
     */
    public function searchAction($q)
    {
        $this->view->assign('q', $q);
        $this->view->assign('items', $this->glossaryRepository->findAllWithQuery($q));
    }

    /**
     * @param Glossary $entry
     * @return void
     */
    public function showAction(Glossary $entry) {

        $this->view->assign('item', $entry);
    }
}
