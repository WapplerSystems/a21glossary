<?php

namespace SveWap\A21glossary\Domain\Repository;

use SveWap\A21glossary\Domain\Model\Glossary;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class GlossaryRepository extends Repository
{
    protected $defaultOrderings = [
        'short' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * @return string[]
     */
    public function findAllForIndex()
    {
        $previousOrderings = $this->defaultOrderings;
        $this->defaultOrderings = [];
        /** @var Query $query */
        $query = $this->createQuery();
        // Get the query parser via object manager to use dependency injection
        $parser = $this->objectManager->get(Typo3DbQueryParser::class);
        // Convert the extbase query to a query builder
        $queryBuilder = $parser->convertQueryToDoctrineQueryBuilder($query);
        // Add our select and group by
        $queryBuilder->selectLiteral('substr(' . $queryBuilder->quoteIdentifier('short') . ', 1, 1) AS ' . $queryBuilder->quoteIdentifier('char'))
            ->groupBy('char');
        $result = $query->statement($queryBuilder)->execute(true);
        $this->defaultOrderings = $previousOrderings;
        return $result;
    }

    /**
     * @param string $char
     *
     * @return Glossary[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllWithChar(string $char)
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
    public function findAllWithQuery(string $q)
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
