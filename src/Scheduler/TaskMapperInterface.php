<?php namespace Flashtalking\DagTaskScheduler;

use League\Period\Period;

interface TaskMapperInterface {
    public function mapTask(array $task, Period $period, $timezone);
}