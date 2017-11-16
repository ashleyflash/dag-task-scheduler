<?php namespace Tests\Integration;
/**
 * Created by PhpStorm.
 * User: ash
 * Date: 21/03/16
 * Time: 09:18
 */

use Flashtalking\DagTaskScheduler\Storage\PDOTaskStorage;

class PDOTaskStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $db;

    protected $dbSchema;

    /**
     * @var
     */
    protected $PDOTaskStorage;

    public function setUp()
    {
        $this->db = new \PDO('mysql:host=localhost;','daguser','q3raTVttAcnHpTTHCUwsGLu9');

        $this->PDOTaskStorage = new PDOTaskStorage($this->db, 'dag_scheduler_int');
    }

    public function testGetProcesses()
    {
        $processes = $this->PDOTaskStorage->getProcesses(false, false);

        $this->assertNotEmpty($processes);
    }

    public function testGetProcessesInTurbo()
    {
        $processes = $this->PDOTaskStorage->getProcesses(true, false);

        $this->assertNotEmpty($processes);
    }

    public function testGetProcessesEnabled()
    {
        $processes = $this->PDOTaskStorage->getProcesses(false, true);

        $this->assertNotEmpty($processes);
    }

    public function testGetDependencies()
    {
        $dependencies = $this->PDOTaskStorage->getDependencies();

        $this->assertNotEmpty($dependencies);
    }

    public function testGetDependenciesForTask()
    {
        $dependencies = $this->PDOTaskStorage->getDependenciesForTask(34);

        $this->assertNotEmpty($dependencies);
    }

    public function testGetDependencyTree()
    {
        $trees = $this->PDOTaskStorage->getDependencyTree();

        $this->assertNotEmpty($trees);
    }

    public function testGetDependencyTreeForTask()
    {
        $trees = $this->PDOTaskStorage->getDependencyTreeForTask(34);

        $this->assertNotEmpty($trees);
    }

    public function testGetTaskName()
    {
        $this->assertNotEmpty($this->PDOTaskStorage->getTaskName(102));
    }
}
