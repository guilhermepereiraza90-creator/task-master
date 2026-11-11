<?php
class TaskPresenter {
    private $model;
    private $view;

    public function __construct(Task $model, TaskViewInterface $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function index() {
        try {
            $tasks = $this->model->getAll();
           
            // Regra do MVP: Formatação da Apresentação
            foreach ($tasks as &$task) {
                $task['title'] = strtoupper($task['title']);
            }
           
            $this->view->displayTasks($tasks);
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }

    public function create($title, $description, $dueDate, $assignee) {
        try {
            $this->model->save($title, $description, $dueDate, $assignee);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }

    public function complete($id) {
        $this->model->complete($id);
        header("Location: index.php");
        exit;
    }

    public function delete($id) {
        $this->model->delete($id);
        header("Location: index.php");
        exit;
    }
}
?>