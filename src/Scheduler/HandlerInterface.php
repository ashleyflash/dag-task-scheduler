<?php namespace DagTaskScheduler;

interface HandlerInterface {

    public function handle(array $task, array $params);
}
