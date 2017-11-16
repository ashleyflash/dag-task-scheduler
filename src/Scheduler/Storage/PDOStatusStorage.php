<?php namespace Flashtalking\DagTaskScheduler\Storage;

use Flashtalking\DagTaskScheduler\PeriodHelper;
use League\Period\Period;

class PDOStatusStorage implements StatusStorageInterface
{
    const STATUS_RUNNING = 50;
    const STATUS_COMPLETE = 95;
    const STATUS_PENDING = 20;
    const STATUS_FAILED = 60;

    const DATE_SQL = 'Y-m-d H:i:s';

    protected $db;

    protected $dbSchema;

    public function __construct(\PDO $db, $schema)
    {
        $this->db = $db;

        $this->dbSchema = $schema;
    }

    public function isComplete($idDependency, $periods)
    {
        foreach ($periods as $period) {

            $counted = $this->countLocks($idDependency, $period->getStartDate(), self::STATUS_COMPLETE);

            $expected = $this->getExpectedLockValue($idDependency, $period->getStartDate());

            if ($expected === null || $counted < $expected) {

                return false;
            }
        }

        return true;
    }

    public function getRunningTasks(TaskStorageInterface $taskStorageInterface)
    {
        $processStatus = $this->getProcessStatus();

        $count = [];

        foreach ($processStatus as $process_status) {

            $count[$process_status['idprocess_dependency']]['pending'] = $process_status['pending'];
            $count[$process_status['idprocess_dependency']]['running'] = $process_status['running'];
            $count[$process_status['idprocess_dependency']]['failing'] = $process_status['failing'];
        }

        $dependency = [];

        $dependencyCounts = $this->getDependencyStatusCounts();

        foreach ($dependencyCounts as $dependencyCount) {

            $dependency[$dependencyCount['idprocess_dependency']]['all_total'] = $dependencyCount['all_total'];
            $dependency[$dependencyCount['idprocess_dependency']]['enabled'] = $dependencyCount['enabled'];
            $dependency[$dependencyCount['idprocess_dependency']]['disabled'] = $dependencyCount['disabled'];
        }

        $dependent = [];

        $dependentCounts = $this->getDependentStatusCounts();

        foreach ($dependentCounts as $dependentCount) {

            $dependent[$dependentCount['idprocess_dependent']]['all_total'] = $dependentCount['all_total'];
            $dependent[$dependentCount['idprocess_dependent']]['enabled'] = $dependentCount['enabled'];
            $dependent[$dependentCount['idprocess_dependent']]['disabled'] = $dependentCount['disabled'];
        }

        $lastCompleteTime = [];

        $lastCompleted = $this->getLastCompleteForDependency();

        foreach ($lastCompleted as $lastComplete) {

            $lastCompleteTime[$lastComplete['idprocess_dependency']]['last_complete'] = $lastComplete['last_complete'];
        }

        $lastIncompleteTime = [];

        $lastIncompleted = $this->getLastIncompleteForDependency();

        foreach ($lastIncompleted as $lastIncomplete) {

            $lastIncompleteTime[$lastIncomplete['idprocess_dependency']]['last_incomplete'] = $lastIncomplete['last_incomplete'];
            $lastIncompleteTime[$lastIncomplete['idprocess_dependency']]['missed_sla'] = $lastIncomplete['missed_sla'];
        }

        $averageTime = [];

        $averages = $this->getAverageTaskTimesForPrevious24Hours();

        foreach ($averages as $average) {

            $averageTime[$average['idprocess_dependency']]['process_time'] = $average['task_time_taken'];
        }

        $status = [];

        $processes = $taskStorageInterface->getProcesses(false, false);

        foreach ($processes as $process) {

            #TODO missed sla

            $status[] = ['id' => $process['idprocess_dependency'],
                'enabled' => $process['process_enabled'],
                'name' => $process['lock_name'],
                'group' => $this->getProcessGroupName($process['idprocess_dependency_group']),
                'schedule' => $process['process_type'],
                'status' => ['pending' => intval(isset($count[$process['idprocess_dependency']]['pending']) ? $count[$process['idprocess_dependency']]['pending'] : 0),
                    'running' => intval(isset($count[$process['idprocess_dependency']]['running']) ? $count[$process['idprocess_dependency']]['running'] : 0),
                    'failing' => intval(isset($count[$process['idprocess_dependency']]['failing']) ? $count[$process['idprocess_dependency']]['failing'] : 0)],
                'process_time' => isset($averageTime[$process['idprocess_dependency']]['process_time']) ? $averageTime[$process['idprocess_dependency']]['process_time'] : null,
                'last_complete' => isset($lastCompleteTime[$process['idprocess_dependency']]['last_complete']) ? $lastCompleteTime[$process['idprocess_dependency']]['last_complete'] : null,
                'last_incomplete' => isset($lastIncompleteTime[$process['idprocess_dependency']]['last_incomplete']) ? $lastIncompleteTime[$process['idprocess_dependency']]['last_incomplete'] : null,
                'missed_sla' => isset($lastIncompleteTime[$process['idprocess_dependency']]['missed_sla']) ? $lastIncompleteTime[$process['idprocess_dependency']]['missed_sla'] : 0,
                'dependency_all_total' => intval(isset($dependency[$process['idprocess_dependency']]['all_total']) ? $dependency[$process['idprocess_dependency']]['all_total'] : 0),
                'dependency_enabled_total' => intval(isset($dependency[$process['idprocess_dependency']]['enabled']) ? $dependency[$process['idprocess_dependency']]['enabled'] : 0),
                'dependency_disabled_total' => intval(isset($dependency[$process['idprocess_dependency']]['disabled']) ? $dependency[$process['idprocess_dependency']]['disabled'] : 0),
                'dependent_all_total' => intval(isset($dependent[$process['idprocess_dependency']]['all_total']) ? $dependent[$process['idprocess_dependency']]['all_total'] : 0),
                'dependent_enabled_total' => intval(isset($dependent[$process['idprocess_dependency']]['enabled']) ? $dependent[$process['idprocess_dependency']]['enabled'] : 0),
                'dependent_disabled_total' => intval(isset($dependent[$process['idprocess_dependency']]['disabled']) ? $dependent[$process['idprocess_dependency']]['disabled'] : 0),];
        }

        return $status;
    }

    public function getTaskStatusesForTaskId($idprocess_dependency, $status_code)
    {
        $query = $this->db->prepare("SELECT CONCAT(lock_name, ' (',log_file_type,')') as lock_name, lock_value, attempts, log_filename_date, created, started
                                     FROM $this->dbSchema.process_lock JOIN $this->dbSchema.process_dependency USING (idprocess_dependency)
                                     WHERE process_status = ? and lock_value > 0 and idprocess_dependency = ? and log_filename_date >= now() - interval 3 day");

        $query->execute([$status_code, $idprocess_dependency]);

        return $query->fetchAll();
    }

    public function getFailingTasksForTask($id)
    {
        $query = $this->db->prepare("SELECT lock_value, attempt, log_filename_date, process_failure, failure_time
                                     FROM $this->dbSchema.process_failure
                                     WHERE idprocess_dependency = ? AND log_filename_date >= NOW() - INTERVAL 7 DAY ORDER BY failure_time DESC LIMIT 500");

        $query->execute([$id]);

        return $query->fetchAll();
    }

    private function getDependencyStatusCounts()
    {
        $query = $this->db->prepare("SELECT idprocess_dependency, count(*) as all_total, sum(process_enabled='y') as enabled, sum(process_enabled='n') as disabled
                                     FROM $this->dbSchema.process_dependency_tree JOIN
                                     $this->dbSchema.process_dependency USING (idprocess_dependency)
                                     GROUP BY idprocess_dependency");

        $query->execute();

        return $query->fetchAll();
    }

    private function getDependentStatusCounts()
    {
        $query = $this->db->prepare("SELECT idprocess_dependent, count(*) as all_total, sum(process_enabled='y') as enabled, sum(process_enabled='n') as disabled
                                     FROM $this->dbSchema.process_dependency_tree as dt JOIN
                                     $this->dbSchema.process_dependency as d USING (idprocess_dependency)
                                     GROUP BY idprocess_dependent");

        $query->execute();

        return $query->fetchAll();
    }

    public function getProcessStatus()
    {
        $query = $this->db->prepare("SELECT idprocess_dependency, SUM(process_status=?) as pending,
                                     SUM(process_status=50) as running, SUM(process_status=60) as failing FROM  $this->dbSchema.process_lock
                                     WHERE process_status <> 95 and lock_value > 0 and idprocess_dependency > 0 and log_filename_date >= now() - interval 7 day
                                     GROUP BY idprocess_dependency");

        $query->execute([self::STATUS_PENDING]);

        return $query->fetchAll();
    }

    public function getRunningTimeline()
    {
        $query = $this->db->prepare("SELECT count(*) as total, started AS timeline, idprocess_dependency
                                     FROM $this->dbSchema.process_lock
                                     WHERE started >= now() - INTERVAL 6 HOUR AND lock_value <> 0 AND idprocess_dependency <> 0 and log_filename_date >= now() - INTERVAL 7 DAY
                                     GROUP BY DATE_FORMAT(`started`, '%d%H:%i'), idprocess_dependency ORDER BY started");

        $query->execute();

        return $query->fetchAll();
    }

    public function getPendingTimeline()
    {
        $query = $this->db->prepare("SELECT count(*) as total, created AS timeline, idprocess_dependency
                                     FROM $this->dbSchema.process_lock
                                     WHERE created >= now() - INTERVAL 6 HOUR AND lock_value <> 0 AND idprocess_dependency <> 0 and log_filename_date >= now() - INTERVAL 7 DAY
                                     GROUP BY DATE_FORMAT(`started`, '%d%H:%i'), idprocess_dependency ORDER BY created");

        $query->execute();

        return $query->fetchAll();
    }

    public function getAverageTaskTimesForTask($id)
    {
        $query = $this->db->prepare("SELECT avg(timediff(completed, started)) as task_time_taken, UNIX_TIMESTAMP(log_filename_date) as task_time FROM $this->dbSchema.process_lock
                                     WHERE log_filename_date >= now() - INTERVAL 7 DAY
                                     AND idprocess_dependency = ? AND completed <> '0000-00-00 00:00:00' GROUP BY log_filename_date");

        $query->execute([$id]);

        return $query->fetchAll();
    }

    public function getAverageTaskTimes()
    {
        $query = $this->db->prepare("SELECT avg(timediff(completed, started)) as task_time_taken, UNIX_TIMESTAMP(log_filename_date) as task_time, idprocess_dependency FROM $this->dbSchema.process_lock
                                     WHERE log_filename_date >= now() - INTERVAL 7 DAY
                                     AND completed <> '0000-00-00 00:00:00' GROUP BY log_filename_date, idprocess_dependency");

        $query->execute();

        return $query->fetchAll();
    }

    public function getAverageTaskTimesForPrevious24Hours()
    {
        $query = $this->db->prepare("SELECT avg(timediff(completed, started)) as task_time_taken, idprocess_dependency FROM $this->dbSchema.process_lock
                                     WHERE log_filename_date >= now() - INTERVAL 1 DAY
                                     AND completed <> '0000-00-00 00:00:00' GROUP BY idprocess_dependency");

        $query->execute();

        return $query->fetchAll();
    }

    private function getProcessGroupName($idprocess_dependency_group)
    {
        $query = $this->db->prepare("SELECT group_name FROM $this->dbSchema.process_dependency_group WHERE idprocess_dependency_group = ?");

        $query->execute([$idprocess_dependency_group]);

        return $query->fetchColumn();
    }

    private function getLastCompleteForDependency()
    {
        $query = $this->db->prepare("SELECT MAX(completed) as last_complete, idprocess_dependency
                                     FROM $this->dbSchema.process_lock WHERE log_filename_date >= NOW() - INTERVAL 7 DAY
                                     AND process_status = ? AND lock_value > 0 GROUP BY idprocess_dependency");

        $query->execute([self::STATUS_COMPLETE]);

        return $query->fetchAll();
    }

    private function getLastIncompleteForDependency()
    {
        $query = $this->db->prepare("SELECT MIN(l.started) as last_incomplete, IF (time_to_sec(timediff(now(), l.started)) > (d.sla * 60), true, false) as missed_sla, l.idprocess_dependency
                                     FROM $this->dbSchema.process_lock AS l
                                     JOIN $this->dbSchema.process_dependency AS d USING (idprocess_dependency)
                                     WHERE log_filename_date >= NOW() - INTERVAL 7 DAY
                                     AND l.process_status <> ? AND l.started >= NOW() - INTERVAL 7 DAY GROUP BY l.idprocess_dependency");

        $query->execute([self::STATUS_COMPLETE]);

        return $query->fetchAll();
    }

    private function countLocks($idDependency, \DateTimeImmutable $startDate, $status)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM $this->dbSchema.process_lock
                                     WHERE  log_filename_date = ? AND idprocess_dependency = ? AND process_status = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $status]);

        return $query->fetchColumn();
    }

    public function getLockedItems($idDependency, \DateTimeImmutable $startDate)
    {
        $query = $this->db->prepare("SELECT lock_value FROM $this->dbSchema.process_lock
                                     WHERE log_filename_date = ? AND idprocess_dependency = ? AND process_status = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, self::STATUS_COMPLETE]);

        return array_column($query->fetchAll(), 'lock_value', 'lock_value');
    }

    public function setExpectedLockValue($idDependency, \DateTimeImmutable $startDate, $count)
    {
        $query = $this->db->prepare("REPLACE INTO $this->dbSchema.process_lock_expected (log_filename_date,idprocess_dependency,expected_lock_value)
                                     VALUES (?,?,?)");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $count]);

        return $query->rowCount() === 1;

    }

    public function getExpectedLockValue($idDependency, \DateTimeImmutable $startDate)
    {
        $query = $this->db->prepare("SELECT SUM(expected_lock_value) FROM $this->dbSchema.process_lock_expected
                                     WHERE log_filename_date = ? AND idprocess_dependency = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency]);

        return $query->fetchColumn();
    }

    public function dependenciesComplete($idDependency, Period $timePeriod)
    {
        $rows = $this->getDependencyIdsForProcess($idDependency);

        foreach ($rows as $row) {

            $range = $row['process_type'];

            if ($row['process_enabled'] !== 'y' || !$this->isComplete($row['idprocess_dependent'], $timePeriod->split(PeriodHelper::createSafeInterval($range)))) {
                return false;
            }
        }

        return true;
    }

    private function setLock(\DateTimeInterface $startDate, $idDependency, $uniqueValue, $status)
    {
        $query = $this->db->prepare("INSERT INTO $this->dbSchema.process_lock (log_filename_date, idprocess_dependency, lock_value, process_status)
                                     VALUES (?,?,?,?)
                                     ON DUPLICATE KEY UPDATE lock_value = ?, process_status = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue, $status, $uniqueValue, $status]);

        return $query->rowCount() === 1;
    }

    private function addAttempts(\DateTimeInterface $startDate, $idDependency, $uniqueValue)
    {
        $query = $this->db->prepare("UPDATE $this->dbSchema.process_lock SET attempts = attempts + 1
                                     WHERE log_filename_date = ? AND idprocess_dependency = ? AND lock_value = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue]);

        return $query->rowCount() === 1;
    }

    private function updateStarted(\DateTimeInterface $startDate, $idDependency, $uniqueValue)
    {
        $query = $this->db->prepare("UPDATE $this->dbSchema.process_lock SET started = NOW()
                                     WHERE log_filename_date = ? AND idprocess_dependency = ? AND lock_value = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue]);

        return $query->rowCount() === 1;
    }

    private function updateCompleted(\DateTimeInterface $startDate, $idDependency, $uniqueValue)
    {
        $query = $this->db->prepare("UPDATE $this->dbSchema.process_lock SET completed = NOW()
                                     WHERE log_filename_date = ? AND idprocess_dependency = ? AND lock_value = ?");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue]);

        return $query->rowCount() === 1;
    }

    public function complete($id)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        $this->setLock($startDate, $idDependency, $uniqueValue, self::STATUS_COMPLETE);

        return $this->updateCompleted($startDate, $idDependency, $uniqueValue);
    }

    public function pending($id)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        if ($this->selectLastAttempt($id) >= 1) {
            return false;
        }

        return $this->setLock($startDate, $idDependency, $uniqueValue, self::STATUS_PENDING);
    }

    public function running($id)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        $this->setLock($startDate, $idDependency, $uniqueValue, self::STATUS_RUNNING);

        $this->addAttempts($startDate, $idDependency, $uniqueValue);

        return $this->updateStarted($startDate, $idDependency, $uniqueValue);
    }

    public function failing($id, $message)
    {
        $this->insertFailedMessage($id, $message);

        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        return $this->setLock($startDate, $idDependency, $uniqueValue, self::STATUS_FAILED);
    }

    private function insertFailedMessage($id, $message)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        $lastAttempt = $this->selectLastAttempt($id);

        $query = $this->db->prepare("REPLACE INTO $this->dbSchema.process_failure (log_filename_date, idprocess_dependency, lock_value, process_failure, attempt)
                                     VALUES (?,?,?,?,?)");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue, $message, $lastAttempt]);

        return $query->rowCount() === 1;
    }

    private function selectLastAttempt($id)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        $query = $this->db->prepare("SELECT attempts FROM $this->dbSchema.process_lock
                                     WHERE  log_filename_date = ? AND idprocess_dependency = ? AND lock_value = ? ORDER BY attempts DESC LIMIT 1");

        $query->execute([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue]);

        return $query->fetchColumn();
    }

    public function generateId(\DateTimeInterface $startDate, $idDependency, $uniqueValue)
    {
        return json_encode([$startDate->format(self::DATE_SQL), $idDependency, $uniqueValue]);
    }

    public function decodeId($id)
    {
        $values = json_decode($id, true);

        if (count($values) !== 3) {
            throw new \InvalidArgumentException("Invalid dependency ID");
        }

        return [new \DateTime($values[0]), $values[1], $values[2]];
    }

    private function getDependencyIdsForProcess($ids)
    {
        $query = $this->db->prepare("SELECT tree.idprocess_dependency, idprocess_dependent, process_enabled, process_type, dep.lock_name FROM $this->dbSchema.process_dependency_tree AS tree
                                     JOIN  $this->dbSchema.process_dependency AS dep ON dep.idprocess_dependency = tree.idprocess_dependent WHERE tree.idprocess_dependency = ?");

        $query->execute([$ids]);

        return $query->fetchAll();
    }

    public function stats($id, $start, $end)
    {
        list($startDate, $idDependency, $uniqueValue) = $this->decodeId($id);

        $query = $this->db->prepare("REPLACE INTO $this->dbSchema.process_stats (log_filename_date,log_filename_date_only,idprocess_dependency,lock_value,created,ended)
                                     VALUES (?,?,?,?,?,?)");

        $query->execute([$startDate->format(self::DATE_SQL), $startDate->format('Y-m-d'), $idDependency, $uniqueValue, $start, $end]);

        return $query->rowCount() === 1;
    }
}
