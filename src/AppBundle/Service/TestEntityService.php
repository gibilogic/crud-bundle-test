<?php

/**
 * @package     Gibilogic\CrudBundle Test
 * @subpackage  AppBundle\Service
 * @author      GiBiLogic <info@gibilogic.com>
 * @authorUrl   http://www.gibilogic.com
 */

namespace AppBundle\Service;

use Gibilogic\CrudBundle\Entity\EntityService;
use AppBundle\Entity\TestEntity;

/**
 * TestEntityService class.
 *
 * @see \Gibilogic\CrudBundle\Entity\EntityService
 */
class TestEntityService extends EntityService
{
    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'AppBundle:TestEntity';
    }

    /**
     * @return string
     */
    public function getEntityPrefix()
    {
        return 'app_test_entity';
    }

    /**
     * @return \AppBundle\Entity\TestEntity
     */
    public function getNewEntity()
    {
        $id = uniqid();
        return new TestEntity($id, $id);
    }
}
