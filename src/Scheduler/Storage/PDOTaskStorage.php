<?php namespace DagTaskScheduler\Storage;

class PDOTaskStorage implements TaskStorageInterface
{
    /**
     * @var
     */
    protected $db;

    protected $dbSchema;

    public function __construct(\PDO $db, $schema)
    {
        $this->db = $db;

        $this->dbSchema = $schema;
    }

    public function getProcesses($turboMode, $enabled)
    {
        $sqlQuery = "SELECT *, CONCAT(lock_name, ' (',log_file_type,')') as lock_name FROM $this->dbSchema.process_dependency WHERE idprocess_dependency > 0";

        $build_params = [];

        if ($turboMode) {

            $sqlQuery .= " AND suspended_in_turbo <> ?";
            $build_params[] = "y";
        }

        if ($enabled) {

            $sqlQuery .= " AND process_enabled = ?";
            $build_params[] = "y";
        }

        $query = $this->db->prepare($sqlQuery);

        $query->execute($build_params);

        return $query->fetchAll();
    }

    public function getDependencies()
    {
        $query = $this->db->prepare("SELECT idprocess_dependency as `id`, CONCAT(lock_name, ' (',log_file_type,')') as label, process_enabled as enabled FROM $this->dbSchema.process_dependency");

        $query->execute();

        return $query->fetchAll();
    }

    public function getDependenciesForTask($id)
    {
        $query = $this->db->prepare("SELECT pd.idprocess_dependency as `id`, CONCAT(pd.lock_name, ' (',pd.log_file_type,')') as label, pd.process_enabled as enabled
                                     FROM $this->dbSchema.process_dependency_tree AS dt
                                     JOIN $this->dbSchema.process_dependency as pd ON pd.idprocess_dependency = dt.idprocess_dependent
                                     WHERE dt.idprocess_dependency = ?");

        $query->execute([$id]);

        $result1 = $query->fetchAll();

        $query = $this->db->prepare("SELECT pd.idprocess_dependency as `id`, CONCAT(pd.lock_name, ' (',pd.log_file_type,')') as label, pd.process_enabled as enabled
                                     FROM $this->dbSchema.process_dependency_tree AS dt
                                     JOIN $this->dbSchema.process_dependency as pd USING (idprocess_dependency)
                                     WHERE dt.idprocess_dependent = ?");

        $query->execute([$id]);

        $result2 = $query->fetchAll();

        $query = $this->db->prepare("SELECT idprocess_dependency as `id`, CONCAT(lock_name, ' (',log_file_type,')') as label, process_enabled as enabled
                                     FROM $this->dbSchema.process_dependency WHERE idprocess_dependency = ?");

        $query->execute([$id]);

        $result3 = $query->fetchAll();

        return array_merge($result1, $result2, $result3);
    }

    public function getDependencyTree()
    {
        $query = $this->db->prepare("SELECT dt.idprocess_dependency as `v`, dt.idprocess_dependent as `u` FROM $this->dbSchema.process_dependency_tree as dt
                JOIN  $this->dbSchema.process_dependency as j1 USING (idprocess_dependency)
                JOIN $this->dbSchema.process_dependency as j2 ON j2.idprocess_dependency = dt.idprocess_dependent");

        $query->execute();

        return $query->fetchAll();
    }

    public function getTaskName($id)
    {
        $query = $this->db->prepare("SELECT CONCAT(lock_name, ' (',log_file_type,')') as task_name FROM $this->dbSchema.process_dependency WHERE idprocess_dependency = ?");

        $query->execute([$id]);

        return $query->fetchColumn();
    }

    public function getDependencyTreeForTask($id)
    {
        $query = $this->db->prepare("SELECT dt.idprocess_dependency as `v`, dt.idprocess_dependent as `u` FROM $this->dbSchema.process_dependency_tree as dt
                                     WHERE dt.idprocess_dependency = ? OR dt.idprocess_dependent = ?");

        $query->execute([$id, $id]);

        return $query->fetchAll();
    }
}
