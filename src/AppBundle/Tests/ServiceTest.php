<?php

/**
 * @package     Gibilogic\CrudBundle Test
 * @subpackage  AppBundle\Tests
 * @author      GiBiLogic <info@gibilogic.com>
 * @authorUrl   http://www.gibilogic.com
 */

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ServiceTest.
 *
 * @see \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
 */
class ServiceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * @var \AppBundle\Service\TestEntityService $service
     */
    private $service;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->service = static::$kernel->getContainer()
            ->get('app.entity.test_entity');
    }

    /**
     * Tests for basic functions.
     */
    public function testBasic()
    {
        $this->assertEquals('AppBundle:TestEntity', $this->service->getEntityName());
        $this->assertEquals('app_test_entity', $this->service->getEntityPrefix());
    }

    /**
     * Tests for the "findEntity" method.
     */
    public function testFindEntity()
    {
        /* @var \AppBundle\Entity\TestEntity $entity */
        $entity = $this->service->findEntity('00000001');

        $this->assertNotNull($entity);
        $this->assertEquals('00000001', $entity->getId());
    }

    /**
     * Tests for the "findEntities" method.
     */
    public function testFindEntities()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $result = $this->service->findEntities($request);
        $this->assertNotEmpty($result['entities']);
        $this->assertCount(4, $result['entities']);
        $this->assertEmpty($result['options']['filters']);
        $this->assertEmpty($result['options']['sorting']);
    }

    /**
     * Tests for the "findEntities" method with sorting.
     */
    public function testFindEntitiesSorted()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $request->request->add(array(
            'app_test_entity_sorting_name' => 'desc'
        ));

        $result = $this->service->findEntities($request);
        $this->assertNotEmpty($result['entities']);
        $this->assertCount(4, $result['entities']);
        $this->assertNotEmpty($result['options']['sorting']);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $result['entities'][0];
        $this->assertEquals('Pluto', $firstEntity->getName());
    }

    /**
     * Tests for the "findEntities" method with simple filtering.
     */
    public function testFindEntitiesSimpleFilter()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $request->request->add(array(
            'app_test_entity_filter_name' => 'Pluto'
        ));

        $result = $this->service->findEntities($request);
        $this->assertNotEmpty($result['entities']);
        $this->assertCount(1, $result['entities']);
        $this->assertNotEmpty($result['options']['filters']);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $result['entities'][0];
        $this->assertEquals('Pluto', $firstEntity->getName());
    }

    /**
     * Tests for the "findEntities" method with custom filtering.
     */
    public function testFindEntitiesCustomFilter()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $request->request->add(array(
            'app_test_entity_filter_text' => 'pap'
        ));

        $result = $this->service->findEntities($request);
        $this->assertNotEmpty($result['entities']);
        $this->assertCount(2, $result['entities']);
        $this->assertNotEmpty($result['options']['filters']);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $result['entities'][0];
        $this->assertEquals('Paperino', $firstEntity->getName());
    }

    /**
     * Tests for the "findEntities" method with custom filtering and sorting.
     */
    public function testFindEntitiesCustomFilterSorted()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $request->request->add(array(
            'app_test_entity_sorting_name' => 'desc',
            'app_test_entity_filter_text' => 'pap'
        ));

        $result = $this->service->findEntities($request);
        $this->assertNotEmpty($result['entities']);
        $this->assertCount(2, $result['entities']);
        $this->assertNotEmpty($result['options']['filters']);

        /* @var \AppBundle\Entity\TestEntity $firstEntity */
        $firstEntity = $result['entities'][0];
        $this->assertEquals('Paperoga', $firstEntity->getName());
    }

    /**
     * Tests for the "findEntities" method with pagination.
     */
    public function testFindEntitiesPaginated()
    {
        $request = Request::createFromGlobals();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $result = $this->service->findEntities($request, array(), true);

        /* @var \Doctrine\ORM\Tools\Pagination\Paginator $entities */
        $entities = $result['entities'];

        $this->assertNotEmpty($result['entities']);
        $this->assertCount(2, $entities->getIterator());
        $this->assertEquals(1, $result['options']['page']);
        $this->assertEmpty($result['options']['filters']);
        $this->assertEmpty($result['options']['sorting']);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
