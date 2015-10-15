<?php

/**
 * @package     Gibilogic\CrudBundle Test
 * @subpackage  AppBundle\Tests
 * @author      GiBiLogic <info@gibilogic.com>
 * @authorUrl   http://www.gibilogic.com
 */

namespace AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\AbstractQuery;

/**
 * Class RepositoryTest.
 *
 * @see \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
 */
class RepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test for the "find" method.
     */
    public function testFind()
    {
        /* @var \AppBundle\Entity\TestEntity $entity */
        $entity = $this->getRepository()->find('00000001');

        $this->assertNotNull($entity);
        $this->assertEquals('00000001', $entity->getId());
    }

    /**
     * Test for the "findOneBy" method.
     */
    public function testFindOneBy()
    {
        /* @var \AppBundle\Entity\TestEntity $entity */
        $entity = $this->getRepository()->findOneBy(array('id' => '00000002'));

        $this->assertNotNull($entity);
        $this->assertEquals('00000002', $entity->getId());
    }

    /**
     * Test for the "findAll" method.
     */
    public function testFindAll()
    {
        $entities = $this->getRepository()->findAll();
        $this->assertNotEmpty($entities);
        $this->assertCount(4, $entities);

        /* @var \AppBundle\Entity\TestEntity $anotherEntity */
        $anotherEntity = $entities[2];
        $this->assertEquals('00000003', $anotherEntity->getId());
    }

    /**
     * Test for the "getEntity" method.
     */
    public function testGetEntity()
    {
        /* @var \AppBundle\Entity\TestEntity $entity */
        $entity = $this->getRepository()->getEntity('00000001');

        $this->assertNotNull($entity);
        $this->assertEquals('00000001', $entity->getId());

        $plainEntity = $this->getRepository()->getEntity('00000002', AbstractQuery::HYDRATE_ARRAY);
        $this->assertNotNull($plainEntity);
        $this->assertTrue(is_array($plainEntity));
        $this->assertArrayHasKey('id', $plainEntity);
        $this->assertEquals('00000002', $plainEntity['id']);
    }

    /**
     * Test for the "getEntities" method.
     */
    public function testGetEntities()
    {
        $entities = $this->getRepository()->getEntities();
        $this->assertNotEmpty($entities);
        $this->assertCount(4, $entities);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $entities[0];
        $this->assertEquals('Paperino', $firstEntity->getName());
    }

    /**
     * Test for the "getEntities" method with sorting.
     */
    public function testGetEntitiesSorted()
    {
        $entities = $this->getRepository()->getEntities(array(
            'sorting' => array('id' => 'desc')
        ));
        $this->assertNotEmpty($entities);
        $this->assertCount(4, $entities);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $entities[0];
        $this->assertEquals('00000004', $firstEntity->getId());
    }

    /**
     * Test for the "getEntities" method with filtering.
     */
    public function testGetEntitiesFiltered()
    {
        $entities = $this->getRepository()->getEntities(array(
            'filters' => array('text' => 'paperino')
        ));
        $this->assertNotEmpty($entities);
        $this->assertCount(1, $entities);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $entities[0];
        $this->assertEquals('00000003', $firstEntity->getId());
    }

    /**
     * Test for the "getEntities" method with sorting and filtering.
     */
    public function testGetEntitiesSortedAndFiltered()
    {
        $entities = $this->getRepository()->getEntities(array(
            'sorting' => array('id' => 'desc'),
            'filters' => array('text' => 'pape')
        ));
        $this->assertNotEmpty($entities);
        $this->assertCount(2, $entities);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $entities[0];
        $this->assertEquals('00000004', $firstEntity->getId());
    }

    /**
     * Test for the "getPaginatedEntities" method.
     */
    public function testGetPaginatedEntities()
    {
        $entities = $this->getRepository()->getPaginatedEntities(array(
            'elementsPerPage' => 2,
            'page' => 1
        ));

        $iterator = $entities->getIterator();
        $this->assertNotEmpty($iterator);
        $this->assertEquals(2, $iterator->count());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @return \AppBundle\Entity\TestEntityRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('AppBundle:TestEntity');
    }
}
