<?php namespace DagTaskScheduler\Storage;

interface TaskStorageInterface {
    public function getProcesses($turboMode, $enabled);
    public function getDependencies();
    public function getDependencyTree();
    public function getDependenciesForTask($id);
    public function getDependencyTreeForTask($id);
    public function getTaskName($id);
}
