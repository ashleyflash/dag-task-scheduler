<?php namespace Flashtalking\DagTaskScheduler;

use Phresque\Phresque;

class QueueHandler implements HandlerInterface
{

    private $queue;

    public function __construct(Phresque $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param array $row
     * @param array $params
     * @return null|\Phresque\Model\Job
     */
    public function handle(array $row, array $params)
    {
        return $this->queue->make($row['job_name'], $params)->lock()->push($row['queue'] ?: 'default');
    }
}
