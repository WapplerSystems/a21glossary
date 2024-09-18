<?php

namespace WapplerSystems\A21glossary\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use WapplerSystems\A21glossary\Domain\Model\Glossary;

class GlossaryRepository extends Repository
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }

    protected $defaultOrderings = [
        'short' => QueryInterface::ORDER_ASCENDING
    ];

    public function findAllForIndex()
    {
        /** @var Query $query */
        $query = $this->createQuery();

        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('tx_a21glossary_main');
        $queryBuilder->from('tx_a21glossary_main')
            ->selectLiteral('substr(' . $queryBuilder->quoteIdentifier('short') . ', 1, 1) AS ' . $queryBuilder->quoteIdentifier('char'))
            ->groupBy('char')
            ->orderBy('char', 'ASC');

        return $query->statement($queryBuilder)->execute(true);
    }

    /**
     * @param string $char
     *
     * @return Glossary[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllWithChar(string $char): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->like('short', $char . '%')
        );

        return $query->execute();
    }

    /**
     * @param string $q
     *
     * @return Glossary[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllWithQuery(string $q): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                $query->like('short', '%' . $q . '%'),
                $query->like('shortcut', '%' . $q . '%'),
                $query->like('longversion', '%' . $q . '%'),
                $query->like('description', '%' . $q . '%')
            )
        );

        return $query->execute();
    }
}
