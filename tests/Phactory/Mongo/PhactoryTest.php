<?php

namespace Phactory\Mongo;

class PhactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $db;
    protected $phactory;

    protected function setUp()
    {
        if (!class_exists('Mongo')) {
            $this->markTestIncomplete('Mongo not installed.');
        }

        $this->mongo = new \Mongo();
        $this->db = $this->mongo->testdb;
        $this->phactory = new Phactory($this->db);
    }

    protected function tearDown()
    {
        if (!class_exists('Mongo')) {
            return;
        }
        $this->phactory->reset();

        $this->db->users->drop();
        $this->db->roles->drop();

        $this->mongo->close();
    }

    public function testSetDb()
    {
        $mongo = new \Mongo();
        $db = $mongo->testdb;
        $this->phactory->setDb($db);
        $db = $this->phactory->getDb();
        $this->assertInstanceOf('MongoDB', $db);
    }

    public function testGetDb()
    {
        $db = $this->phactory->getDb();
        $this->assertInstanceOf('MongoDB', $db);
    }

    public function testDefine()
    {
        // test that define() doesn't throw an exception when called correctly
        $this->phactory->define('user', ['name' => 'testuser']);
    }

    public function testDefineWithBlueprint()
    {
        $blueprint = new Blueprint('user', ['name' => 'testuser'], [], $this->phactory);
        $this->phactory->define('user', $blueprint);

        $user = $this->phactory->create('user');
        $this->assertEquals('testuser', $user['name']);
    }

    public function testDefineWithAssociations()
    {
        $this->phactory->define('user',
                         ['name' => 'testuser'],
                         ['role' => $this->phactory->embedsOne('role')]);
    }

    public function testCreate()
    {
        $name = 'testuser';
        $tags = ['one','two','three'];

        // define and create user in db
        $this->phactory->define('user', ['name' => $name, 'tags' => $tags]);
        $user = $this->phactory->create('user');

        // test returned array
        $this->assertInternalType('array', $user);
        $this->assertEquals($user['name'], $name);
        $this->assertEquals($user['tags'], $tags);

        // retrieve and test expected document from database
        $db_user = $this->db->users->findOne();
        $this->assertEquals($name, $db_user['name']);
        $this->assertEquals($tags, $db_user['tags']);
    }

    public function testCreateWithOverrides()
    {
        $name = 'testuser';
        $override_name = 'override_user';

        // define and create user in db
        $this->phactory->define('user', ['name' => $name]);
        $user = $this->phactory->create('user', ['name' => $override_name]);

        // test returned array
        $this->assertInternalType('array', $user);
        $this->assertEquals($user['name'], $override_name);

        // retrieve and test expected document from database
        $db_user = $this->db->users->findOne();
        $this->assertEquals($db_user['name'], $override_name);
    }

    public function testCreateWithAssociations()
    {
        $this->phactory->define('role',
                         ['name' => 'admin']);
        $this->phactory->define('user',
                         ['name' => 'testuser'],
                         ['role' => $this->phactory->embedsOne('role')]);

        $role = $this->phactory->build('role');
        $user = $this->phactory->createWithAssociations('user', ['role' => $role]);

        $this->assertEquals($role['name'], $user['role']['name']);
    }

    public function testCreateWithEmbedsManyAssociation()
    {
        $this->phactory->define('tag',
                         ['name' => 'Test Tag']);
        $this->phactory->define('blog',
                         ['title' => 'Test Title'],
                         ['tags' => $this->phactory->embedsMany('tag')]);

        $tag = $this->phactory->build('tag');
        $blog = $this->phactory->createWithAssociations('blog', ['tags' => [$tag]]);

        $this->assertEquals('Test Tag', $blog['tags'][0]['name']);

        $this->db->blogs->drop();
    }

    public function testDefineAndCreateWithSequence()
    {
        $tags = ['foo$n','bar$n'];
        $this->phactory->define('user', ['name' => 'user\$n', 'tags' => $tags]);

        for ($i = 0; $i < 5; ++$i) {
            $user = $this->phactory->create('user');
            $this->assertEquals("user$i", $user['name']);
            $this->assertEquals(["foo$i", "bar$i"], $user['tags']);
        }
    }

    public function testGet()
    {
        $name = 'testuser';

        // define and create user in db
        $this->phactory->define('user', ['name' => $name]);
        $user = $this->phactory->create('user');

        // get() expected row from database
        $db_user = $this->phactory->get('user', ['name' => $name]);

        // test retrieved db row
        $this->assertInternalType('array', $db_user);
        $this->assertEquals($name, $db_user['name']);
    }

    public function testRecall()
    {
        $name = 'testuser';

        // define and create user in db
        $this->phactory->define('user', ['name' => $name]);
        $user = $this->phactory->create('user');

        // recall() deletes from the db
        $this->phactory->recall();

        // test that the object is gone from the db
        $db_user = $this->db->users->findOne();
        $this->assertNull($db_user);

        // test that the blueprints weren't destroyed too
        $user = $this->phactory->create('user');
        $this->assertEquals($user['name'], $name);
    }
}
