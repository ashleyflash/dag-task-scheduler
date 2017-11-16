<?php namespace Tests\Integration;
/**
 * Created by PhpStorm.
 * User: ash
 * Date: 21/03/16
 * Time: 09:18
 */

use Flashtalking\DagTaskScheduler\PeriodHelper;
use Flashtalking\DagTaskScheduler\Storage\PDOStatusStorage;
use Flashtalking\DagTaskScheduler\Storage\PDOTaskStorage;
use League\Period\Period;

class PDOStatusStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $db;

    protected $dbSchema;

    /**
     * @var
     */
    protected $PDOStatusStorage;

    const TEST_DAILY_DATE           = "201505210000";

    const END_TEST_DAILY_DATE       = "201505220000";

    public function setUp()
    {
        $this->db = new \PDO('mysql:host=localhost;','daguser','q3raTVttAcnHpTTHCUwsGLu9');

        $this->dbSchema = 'dag_scheduler_int';

        $this->PDOStatusStorage = new PDOStatusStorage($this->db, $this->dbSchema);
    }

    public function testInsertNewLocks()
    {
        $this->db->query("INSERT INTO $this->dbSchema.process_lock (idprocess_dependency, lock_value, log_filename_date, process_status, attempts)
                          VALUES (250,1,NOW(),0,1),(2,2,NOW(),20,1),(4,1,NOW(),50,1)");
    }

    private function getLastDate()
    {
        $query = $this->db->prepare("SELECT log_filename_date FROM $this->dbSchema.process_lock WHERE idprocess_dependency = ?");

        $query->execute([250]);

        return $query->fetchColumn();
    }

    private function getTestCompleteLastDate()
    {
        $query = $this->db->prepare("SELECT log_filename_date FROM $this->dbSchema.process_lock WHERE idprocess_dependency = ?");

        $query->execute([250]);

        return $query->fetchColumn();
    }

    public function testPending()
    {
        $filename_date = $this->getLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),250,1]);

        $this->PDOStatusStorage->pending($id);
    }

    public function testRunning()
    {
        $filename_date = $this->getLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),250,1]);

        $this->PDOStatusStorage->running($id);
    }

    public function testCompleteRunning()
    {
        $filename_date = $this->getTestCompleteLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),4,1]);

        $this->PDOStatusStorage->running($id);
    }

    public function testFailing()
    {
        $filename_date = $this->getLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),250,1]);

        $this->PDOStatusStorage->failing($id, 'failure message');
    }

    public function testPendingAfterFailure()
    {
        $filename_date = $this->getLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),250,1]);

        $this->PDOStatusStorage->pending($id);
    }

    public function testProcessStats()
    {
        $filename_date = $this->getLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),250,1]);

        $this->PDOStatusStorage->stats($id, self::TEST_DAILY_DATE, self::END_TEST_DAILY_DATE);
    }

    public function testRunningTasks()
    {
        $tasks = $this->PDOStatusStorage->getRunningTasks(new PDOTaskStorage($this->db, $this->dbSchema));

        $this->assertNotEmpty($tasks);
    }

    public function testIndividualTaskStatus()
    {
        $running = $this->PDOStatusStorage->getTaskStatusesForTaskId(4, PDOStatusStorage::STATUS_RUNNING);

        $this->assertNotEmpty($running);

        $pending = $this->PDOStatusStorage->getTaskStatusesForTaskId(2, PDOStatusStorage::STATUS_PENDING);

        $this->assertNotEmpty($pending);
    }

    public function testComplete()
    {
        $filename_date = $this->getTestCompleteLastDate();

        $date = new \DateTimeImmutable($filename_date);

        $id = json_encode([$date->format(PDOStatusStorage::DATE_SQL),4,1]);

        $this->PDOStatusStorage->complete($id);
    }

    public function testAverageTaskTimes()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getAverageTaskTimes());
    }

    public function testAverageTaskTimesForTask()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getAverageTaskTimesForTask(4));
    }

    public function testItGetsRunningTimeline()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getRunningTimeline());
    }

    public function testItGetsPendingTimeline()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getPendingTimeline());
    }

    public function testItFailsADependency()
    {
        $period = PeriodHelper::createDateTimePeriod(new \DateTimeImmutable('2015-05-21 09:00:00'), new \DateTimeImmutable('2015-05-21 10:00:00'), 'hour', 0);

        $this->assertFalse($this->PDOStatusStorage->isComplete(10, $period));
    }

    public function testItCompletesAnEncodedId()
    {
        $id = json_encode(array('2015-05-21 09:00:00', 102, 9999));

        $result = $this->PDOStatusStorage->complete($id);

        $this->assertTrue($result);

        $id = json_encode(array('2015-05-21 09:00:00', 102, 31003));

        $this->PDOStatusStorage->complete($id);
    }

    public function testItPassesADependency()
    {
        $period = new Period('2015-05-21 09:00:00','2015-05-21 10:00:00');

        $this->assertTrue($this->PDOStatusStorage->isComplete(102, $period));
    }

    public function testItPassesDependencies()
    {
        $period = new Period('2015-05-21 09:00:00','2015-05-21 10:00:00');

        $this->assertTrue($this->PDOStatusStorage->dependenciesComplete(30, $period));
    }

    public function testItGetsLockItems()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getLockedItems(102, new \DateTimeImmutable('2015-05-21 09:00:00')));
    }

    public function testItSetsExpectedLockValue()
    {
        $this->PDOStatusStorage->setExpectedLockValue(102, new \DateTimeImmutable('2015-05-21 23:00:00'), 1);
    }

    public function testItGeneratesID()
    {
        $period = new Period('2015-05-21 09:00:00','2015-05-21 10:00:00');

        $this->PDOStatusStorage->generateId($period->getStartDate(), 102, "something");
    }

    public function testItGetsFailingTasksForTask()
    {
        $this->assertNotEmpty($this->PDOStatusStorage->getFailingTasksForTask(250));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid dependency ID
     */
    public function testItThrowsExceptionForInvalidEncodedId()
    {
        $id = json_encode(array('2014-05-01 10:11:12', '2014-06-02 13:14:15'));

        $this->PDOStatusStorage->complete($id);
    }
}
