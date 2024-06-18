<?php

namespace SveWap\A21glossary\Domain\Repository;

use SveWap\A21glossary\Domain\Model\Glossary;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\QueryBuilder;
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
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $language = $languageAspect->getId();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_a21glossary_main')->createQueryBuilder();
        return $queryBuilder->select('uid')
            ->selectLiteral('substr(' . $queryBuilder->quoteIdentifier('short') . ', 1, 1) AS ' . $queryBuilder->quoteIdentifier('char'))
            ->from('tx_a21glossary_main')
            ->where(
                $queryBuilder->expr()->eq(
                    'sys_language_uid', $queryBuilder->createNamedParameter($language, \PDO::PARAM_INT)
                )
            )
            ->groupBy('char')
            ->execute()
            ->fetchAll();
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
