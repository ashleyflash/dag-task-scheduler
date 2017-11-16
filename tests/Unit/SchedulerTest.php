<?php

class SchedulerTest extends \PHPUnit_Framework_TestCase {

    private $taskStorage;
    private $statusStorage;
    private $handler;
    private $taskMapper;
    private $logger;

    private function getSchedulerMock()
    {
        $this->taskStorage = $this->getMock('Flashtalking\DagTaskScheduler\Storage\TaskStorageInterface');
        $this->statusStorage = $this->getMock('Flashtalking\DagTaskScheduler\Storage\StatusStorageInterface');
        $this->handler = $this->getMock('Flashtalking\DagTaskScheduler\HandlerInterface');
        $this->taskMapper = $this->getMock('Flashtalking\DagTaskScheduler\TaskMapperInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        return new \Flashtalking\DagTaskScheduler\Scheduler($this->taskStorage, $this->statusStorage, $this->handler, $this->taskMapper);

    }

    public function testItPushesTasksToQueue()
    {
        $scheduler = $this->getSchedulerMock();

        $scheduler->setLogger($this->logger);

        $taskRow = [
            'idprocess_dependency' => 2,
            'task_name' => 'foo',
            'duplicate_for_timezones' => false,
            'start_after' => '2016-03-18 00:00:00',
            'process_type' => 'day',
            'queue_offset' => '',
            'sequential' => false,
            'force_push' => false,
            'log_file_type' => 'i',
            'idft_x_server_event_type' => 'fizz',
        ];

        #TODO - figure out the period
        $expectedPeriod = $this->anything();

        $this->taskStorage->method('getProcesses')->with(false, true)->willReturn([
            $taskRow
        ]);

        $this->statusStorage->method('dependenciesComplete')->with(2, $expectedPeriod)->willReturn(true);

        $this->taskMapper->method('mapTask')->with($taskRow, $expectedPeriod, 0)->willReturn(['bar', function(){
            return [1];
        }]);

        $scheduler->runScheduler(new DateTimeImmutable('2016-03-20 01:00:00'), new DateTimeImmutable('2016-03-22 01:00:00'), false);
    }

    public function testItPushesForcePush()
    {
        $scheduler = $this->getSchedulerMock();

        $scheduler->setLogger($this->logger);

        $taskRow = [
            'idprocess_dependency' => 2,
            'task_name' => 'foo',
            'duplicate_for_timezones' => false,
            'start_after' => '2016-03-18 00:00:00',
            'process_type' => 'day',
            'queue_offset' => 2,
            'sequential' => false,
            'force_push' => 1,
            'log_file_type' => 'i',
            'idft_x_server_event_type' => 'fizz',
            'split_type' => 'bar'
        ];

        $period = new \League\Period\Period(new DateTimeImmutable('2016-03-20 00:00:00'), new DateTimeImmutable('2016-03-20 23:59:59'));

        $this->taskStorage->method('getProcesses')->with(false, true)->willReturn([
            $taskRow
        ]);

        $this->statusStorage->method('dependenciesComplete')->with(2, $period)->willReturn(true);

        $this->taskMapper->method('mapTask')->with($taskRow, $period, 0)->willReturn(['bar', function(){
            return [1];
        }]);

        $this->statusStorage->method('isComplete')->with(2, [$period])->willReturn(true);

        $scheduler->runScheduler(new DateTimeImmutable('2016-03-20 00:00:00'), new DateTimeImmutable('2016-03-21 00:00:00'), false);
    }

    public function testItSetsTimezones()
    {
        $scheduler = $this->getSchedulerMock();

        $scheduler->setTimezones([1,2,3]);
    }
}
