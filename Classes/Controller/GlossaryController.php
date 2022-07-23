<?php

namespace WapplerSystems\A21glossary\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use WapplerSystems\A21glossary\Domain\Model\Glossary;

class GlossaryController extends ActionController
{
    /**
     * @var \WapplerSystems\A21glossary\Domain\Repository\GlossaryRepository
     * @inject
     */
    protected $glossaryRepository;

    /**
     * @param string $char
     *
     * @throws InvalidQueryException
     */
    public function indexAction($char = null)
    {
        $this->view->assign('index', $this->glossaryRepository->findAllForIndex());
        if (!empty($char)) {
            $this->view->assign('currentChar', $char);
            $this->view->assign('glossaryItems', $this->glossaryRepository->findAllWithChar($char));
        } else {
            $this->view->assign('glossaryItems', $this->glossaryRepository->findAll());
        }
    }

    /**
     * @param string $q
     *
     * @throws InvalidQueryException
     */
    public function searchAction($q)
    {
        $this->view->assign('q', $q);
        $this->view->assign('glossaryItems', $this->glossaryRepository->findAllWithQuery($q));
    }

    /**
     * @param Glossary $entry
     * @return void
     */
    public function showAction(Glossary $entry) {

        $this->view->assign('item', $entry);
    }
}
