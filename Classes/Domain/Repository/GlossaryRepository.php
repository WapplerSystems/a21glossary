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

    public function findAllFiltered(bool $showAbbreviationsOnlyInGlossaryList)
    {
        $query = $this->createQuery();

        if ($showAbbreviationsOnlyInGlossaryList) {
            $constraints = [$query->equals('shorttype', 'abbr')];
            $query->matching($constraints[0]);
        }

        return $query->execute();
    }

    public function findAllForIndex(bool $showAbbreviationsOnlyInGlossaryList)
    {
        /** @var Query $query */
        $query = $this->createQuery();

        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('tx_a21glossary_main');
        $queryBuilder->from('tx_a21glossary_main')
            ->selectLiteral('substr(' . $queryBuilder->quoteIdentifier('short') . ', 1, 1) AS ' . $queryBuilder->quoteIdentifier('char'))
            ->groupBy('char')
            ->orderBy('char', 'ASC');

        if ($showAbbreviationsOnlyInGlossaryList) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    'shorttype',
                    $queryBuilder->createNamedParameter('abbr')
                )
            );
        }

        return $query->statement($queryBuilder)->execute(true);
    }

    /**
     * @param string $char
     * @param bool $showAbbreviationsOnlyInGlossaryList 
     *
     * @return Glossary[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllWithChar(string $char, bool $showAbbreviationsOnlyInGlossaryList): QueryResultInterface|array
    {
        $query = $this->createQuery();
        
        $constraints = [$query->like('short', $char . '%')];

        if ($showAbbreviationsOnlyInGlossaryList) {
            $constraints[] = $query->equals('shorttype', 'abbr');
        }

        if (count($constraints) > 1) {
            $query->matching($query->logicalAnd(...$constraints));
        } else {
            $query->matching($constraints[0]);
        }

        return $query->execute();
    }

    /**
     * @param string $q
     * @param bool $showAbbreviationsOnlyInGlossaryList 
     *
     * @return Glossary[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllWithQuery(string $q, bool $showAbbreviationsOnlyInGlossaryList): QueryResultInterface|array
    {
        $query = $this->createQuery();

        $orConstraints = [$query->like('short', '%' . $q . '%'), $query->like('shortcut', '%' . $q . '%'), $query->like('longversion', '%' . $q . '%'), $query->like('description', '%' . $q . '%') ];

        if ($showAbbreviationsOnlyInGlossaryList) {
            $query->matching(
                $query->logicalAnd(
                    $query->logicalOr(...$orConstraints),
                    $query->equals('shorttype', 'abbr')
                )
            );
        }
        else {
            $query->matching(
                $query->logicalOr(...$orConstraints)
            );
        }

        return $query->execute();
    }
}
