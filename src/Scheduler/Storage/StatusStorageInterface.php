<?php namespace Flashtalking\DagTaskScheduler\Storage;

use League\Period\Period;

interface StatusStorageInterface {

    public function getRunningTasks(TaskStorageInterface $taskStorageInterface);

    public function getTaskStatusesForTaskId($idprocess_dependency, $status_code);

    public function getFailingTasksForTask($id);

    public function getAverageTaskTimes();

    public function getAverageTaskTimesForTask($id);

    public function getRunningTimeline();

    public function getPendingTimeline();

    public function isComplete($idDependency, $periods);

    public function getLockedItems($idDependency, \DateTimeImmutable $startDate);

    public function setExpectedLockValue($idDependency, \DateTimeImmutable $startDate, $count);

    public function getExpectedLockValue($idDependency, \DateTimeImmutable $startDate);

    public function dependenciesComplete($idDependency, Period $timePeriod);

    public function complete($id);

    public function pending($id);

    public function running($id);

    public function failing($id, $message);

    public function stats($id, $start, $end);

    #TODO - needs to come out
    public function generateId(\DateTimeInterface $startDate, $idDependency, $uniqueValue);
}
