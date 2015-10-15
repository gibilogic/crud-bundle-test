<?php

/**
 * @package     Gibilogic\CrudBundle Test
 * @subpackage  AppBundle\Entity
 * @author      GiBiLogic <info@gibilogic.com>
 * @authorUrl   http://www.gibilogic.com
 */

namespace AppBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Gibilogic\CrudBundle\Entity\EntityRepository;

/**
 * TestEntityRepository class.
 *
 * @see \Gibilogic\CrudBundle\Entity\EntityRepository
 */
class TestEntityRepository extends EntityRepository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param mixed $value
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addTextFilter(QueryBuilder $queryBuilder, $value)
    {
        if (empty($value)) {
            return $queryBuilder;
        }

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('e.id', "'%$value%'"),
                $queryBuilder->expr()->like('e.name', "'%$value%'")
            ));
    }

    /**
     * Returns a list of sortable fields.
     *
     * @return array
     */
    protected function getSortableFields()
    {
        return array('id' => true, 'name' => true);
    }

    /**
     * Returns the default sorting for the entity.
     *
     * @return array
     */
    protected function getDefaultSorting()
    {
        return array('name' => 'asc');
    }
}
