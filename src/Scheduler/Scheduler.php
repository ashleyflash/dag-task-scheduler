<?php namespace Flashtalking\DagTaskScheduler;

use DagTaskScheduler\Storage\StatusStorageInterface;
use DagTaskScheduler\Storage\TaskStorageInterface;
use League\Period\Period;
use Psr\Log\LoggerAwareTrait;

class Scheduler
{
    use LoggerAwareTrait;

    /**
     * @var TaskStorageInterface
     */
    private $taskStorageInterface;

    /**
     * @var StatusStorageInterface
     */
    private $statusStorageInterface;

    private $taskMapper;

    private $handler;

    private $timezones = [0];

    /**
     * @param TaskStorageInterface $taskStorageInterface
     * @param StatusStorageInterface $statusStorageInterface
     * @param HandlerInterface $handler
     * @param TaskMapperInterface $taskMapper
     */
    public function __construct(TaskStorageInterface $taskStorageInterface, StatusStorageInterface $statusStorageInterface, HandlerInterface $handler, TaskMapperInterface $taskMapper)
    {
        $this->taskStorageInterface = $taskStorageInterface;

        $this->statusStorageInterface = $statusStorageInterface;

        $this->handler = $handler;

        $this->taskMapper = $taskMapper;
    }

    public function setTimezones(array $timezones)
    {
        $this->timezones = $timezones;
    }

    /**
     * @param \DateTimeImmutable $startTime
     * @param \DateTimeImmutable $endTime
     * @param $turboMode
     */
    public function runScheduler(\DateTimeImmutable $startTime, \DateTimeImmutable $endTime, $turboMode)
    {
        $tasks = $this->taskStorageInterface->getProcesses($turboMode, true);

        foreach ($tasks as $task) {

            $timezones = $task['duplicate_for_timezones'] ? $this->timezones : [0];

            $startAfter = new \DateTimeImmutable($task['start_after']);

            foreach ($timezones as $timezone) {

                $taskPeriods = PeriodHelper::createDateTimePeriod($startTime, $endTime, $task['process_type'], $timezone);

                foreach ($taskPeriods as $taskPeriod) {

                    $this->logger->info("Start After: ".$taskPeriod->getStartDate()->format('Y-m-d H:i:s')." Task: ".$task['idprocess_dependency']);

                    if ($taskPeriod->isAfter($startAfter))
                    {
                        if ($this->checkTaskDelayOffsetElapsed($task['queue_offset'], $taskPeriod) &&
                            $this->statusStorageInterface->dependenciesComplete($task['idprocess_dependency'], $taskPeriod)) {

                            $this->pushDependency($task, $taskPeriod, $timezone);
                        }

                        if ($task['sequential'] === 'y' && !$this->statusStorageInterface->isComplete($task['idprocess_dependency'], [$taskPeriod])) {

                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Checks that the real time is {$queueOffset} hours beyond the task end date
     *
     * @param $queueOffset
     * @param Period $timePeriod
     * @return bool
     */
    private function checkTaskDelayOffsetElapsed($queueOffset, Period $timePeriod)
    {
        if (!$queueOffset) {
            return true;
        }

        $endsBeforeDate = new \DateTimeImmutable((-1 * $queueOffset) . " hours");

        return $timePeriod->isBefore($endsBeforeDate);
    }

    /**
     * @param array $row
     * @param Period $timePeriod
     * @param int $timezone
     */
    public function pushDependency(array $row, Period $timePeriod, $timezone)
    {
        list($splitArgument, $split) = $this->taskMapper->mapTask($row, $timePeriod, $timezone);

        $callback = function ($dependencyId, $value) use ($row, $timePeriod, $timezone, $splitArgument) {

            $args = [
                'hour'          => $timePeriod->getStartDate()->format("YmdHi"),
                'period'        => $timePeriod,
                'logtype'       => $row['log_file_type'],
                'gmt_offset'    => $timezone,
                'dependency'    => $dependencyId,
                $splitArgument  => $value
            ];

            if ($row['idft_x_server_event_type']) {
                $args['idft_x_server_event_type'] = $row['idft_x_server_event_type'];
            }

            $this->handler->handle($row, $args);
        };

        $this->push($row['idprocess_dependency'], $timePeriod, $split, $callback, $row['force_push']);
    }

    /**
     * @param $dependencyId
     * @param Period $timePeriod
     * @param \Closure $getItemSet
     * @param \Closure $callback
     * @param $forcePush
     * @return $this
     */
    private function push($dependencyId, Period $timePeriod, \Closure $getItemSet, \Closure $callback, $forcePush)
    {
        if ($forcePush == 1) {
            $this->statusStorageInterface->setExpectedLockValue($dependencyId, $timePeriod->getStartDate(), count($getItemSet()));
        }

        if ($this->statusStorageInterface->isComplete($dependencyId, [$timePeriod])) {
            return;
        }

        if ($forcePush != 1) {
            $this->statusStorageInterface->setExpectedLockValue($dependencyId, $timePeriod->getStartDate(), count($getItemSet()));
        }

        $lockedItems = $this->statusStorageInterface->getLockedItems($dependencyId, $timePeriod->getStartDate());

        foreach ($getItemSet() as $item) {
            if (!isset($lockedItems[$item])) {

                $id = $this->statusStorageInterface->generateId($timePeriod->getStartDate(), $dependencyId ,$item);

                $this->statusStorageInterface->pending($id);

                $callback($id, $item);
            }
        }
    }
}
