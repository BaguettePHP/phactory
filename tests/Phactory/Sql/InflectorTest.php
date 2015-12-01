<?php

namespace Phactory\Sql;

/**
 * Test class for Phactory_Inflector.
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        Inflector::reset();
    }

    public function inflectionProvider()
    {
        return [
            ['test', 'tests'],
            ['octopus', 'octopuses'],
            ['fish', 'fish'],
            ['user', 'users'],
        ];
    }

    public function inflectionExceptionProvider()
    {
        return [
            ['fish', 'fishes'],
            ['content', 'content'],
            ['anecdote', 'data'],
        ];
    }

    /**
     * @dataProvider inflectionProvider
     */
    public function testPluralize($singular, $plural)
    {
        $this->assertEquals(Inflector::pluralize($singular), $plural);
    }

    /**
     * @dataProvider inflectionExceptionProvider
     */
    public function testAddException($singular, $plural)
    {
        $this->assertNotEquals(Inflector::pluralize($singular), $plural);
        Inflector::addException($singular, $plural);
        $this->assertEquals(Inflector::pluralize($singular), $plural);
    }
}
