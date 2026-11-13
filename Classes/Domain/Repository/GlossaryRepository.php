<?php

namespace WapplerSystems\A21glossary\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

    /**
     * All entries of the frontend list, without those excluded from it
     *
     * @return Glossary[]|QueryResultInterface
     */
    public function findAll(): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching($query->equals('exclude', false));

        return $query->execute();
    }

    public function findAllForIndex()
    {
        /** @var Query $query */
        $query = $this->createQuery();

        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('tx_a21glossary_main');
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        $queryBuilder->from('tx_a21glossary_main')
            ->selectLiteral('substr(' . $queryBuilder->quoteIdentifier('short') . ', 1, 1) AS ' . $queryBuilder->quoteIdentifier('char'))
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter($query->getQuerySettings()->getStoragePageIds(), Connection::PARAM_INT_ARRAY)
                ),
                $queryBuilder->expr()->in(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter([-1, $query->getQuerySettings()->getLanguageAspect()->getContentId()], Connection::PARAM_INT_ARRAY)
                ),
                $queryBuilder->expr()->eq('exclude', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            )
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
            $query->logicalAnd(
                $query->like('short', $this->escapeLikeWildcards($char) . '%'),
                $query->equals('exclude', false)
            )
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
        $pattern = '%' . $this->escapeLikeWildcards($q) . '%';
        $query->matching(
            $query->logicalAnd(
                $query->logicalOr(
                    $query->like('short', $pattern),
                    $query->like('shortcut', $pattern),
                    $query->like('longversion', $pattern),
                    $query->like('description', $pattern)
                ),
                $query->equals('exclude', false)
            )
        );

        return $query->execute();
    }

    /**
     * Escape % and _ so user input is matched literally
     */
    protected function escapeLikeWildcards(string $value): string
    {
        return $this->connectionPool
            ->getConnectionForTable('tx_a21glossary_main')
            ->escapeLikeWildcards($value);
    }
}
