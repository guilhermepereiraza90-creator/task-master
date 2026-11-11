<?php
// Autoload atualizado para incluir Presenter
spl_autoload_register(function ($class) {
    $dirs = ['Model', 'Presenter', 'View'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/src/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Conexão com o banco SQLite
$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciação das camadas do MVP
$model = new Task($pdo);
$view = new TaskHtmlView();
$presenter = new TaskPresenter($model, $view);

// Roteamento
$action = $_GET['action'] ?? 'index';

if ($action === 'create') {
    $presenter->create($_POST['title'] ?? '', $_POST['description'] ?? '', $_POST['due_date'] ?? '', $_POST['assignee'] ?? '');
} elseif ($action === 'complete') {
    $presenter->complete($_GET['id'] ?? 0);
} elseif ($action === 'delete') {
    $presenter->delete($_GET['id'] ?? 0);
} else {
    $presenter->index();
}
?>