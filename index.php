<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Tudo misturado)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA (Acoplamento de Infraestrutura)
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    assignee TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    
     // ADICIONADO: Captura dos novos inputs do formulário
    $description = trim($_POST['description'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $assignee = trim($_POST['assignee'] ?? '');

    // Regra de negócio solta no meio do arquivo
    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    } elseif (empty($due_date)) {
        $error = 'A data de vencimento é obrigatória!';
    } elseif (empty($assignee)) {
        $error = "O responsável pela tarefa é obrigatório!";
    } else {
         //  MODIFICADO: O INSERT agora inclui os novos campos
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, assignee) VALUES (:title, :description, :due_date, :assignee)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':assignee', $assignee);
        $stmt->execute();

        // Redirecionamento misturado com a lógica
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }
    
    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti</title>
    <style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background-color: #f3f4f6; 
    color: #333; 
    display: flex; 
    justify-content: center; 
    padding: 50px 20px; 
}
.container { 
    background: #fff; 
    padding: 30px; 
    border-radius: 8px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    width: 100%; 
    max-width: 500px; 
}
h1 { 
    font-size: 1.5rem; 
    text-align: center; 
    border-bottom: 2px solid #eee; 
    padding-bottom: 10px; 
}
.error { 
    color: #dc2626; 
    background: #fee2e2; 
    padding: 10px; 
    border-radius: 4px; 
    font-size: 0.9rem; 
    margin-bottom: 15px;
}

/* Força os elementos do formulário a ficarem um abaixo do outro com espaçamento uniforme */
.form-vertical { 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
    margin-top: 20px; 
    margin-bottom: 20px; 
}

/* Coloca a Data e o Responsável lado a lado dividindo o espaço igualmente */
.form-row { 
    display: flex; 
    gap: 10px; 
}
.form-row input { 
    flex: 1; 
    width: 100%;
}

/* Aplica o design padrão para todos os campos de texto e área de descrição */
input[type="text"], input[type="date"], textarea { 
    box-sizing: border-box;
    width: 100%;
    padding: 10px; 
    border: 1px solid #ccc; 
    border-radius: 4px; 
    font-family: inherit;
}

button { 
    background: #2563eb; 
    color: white; 
    border: none; 
    padding: 10px 15px; 
    border-radius: 4px; 
    cursor: pointer; 
    font-weight: bold;
}
button:hover { 
    background: #1d4ed8; 
}

ul { 
    list-style: none; 
    padding: 0; 
}
li { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    padding: 12px; 
    border-bottom: 1px solid #eee; 
}
li.done .task-title { 
    text-decoration: line-through; 
    color: #9ca3af; 
}

/* Organiza o bloco de textos da tarefa alinhados à esquerda em formato de coluna */
.task-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.task-title {
    font-weight: bold;
}
.task-desc {
    font-size: 0.9rem;
    color: #6b7280;
}
.task-meta {
    font-size: 0.8rem;
    color: #4b5563;
}

/* Garante que os botões de ação fiquem sempre visíveis à direita */
.actions {
    display: flex;
    gap: 8px;
}
.actions a { 
    text-decoration: none; 
    margin-left: 5px; 
    cursor: pointer; 
}

    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Edition)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

<!--  MODIFICADO: Classe mudou para 'form-vertical' e novos inputs foram inseridos -->
<form method="POST" action="index.php" class="form-vertical">
    <input type="text" name="title" placeholder="O que precisa ser feito? (Obrigatório)" autocomplete="off">
    
    <!--  ADICIONADO: Campo para Descrição -->
    <textarea name="description" placeholder="Descrição da tarefa... (Opcional)" rows="2"></textarea>
    
    <!--  ADICIONADO: Linha com Data de Vencimento e Responsável -->
    <div class="form-row">
        <input type="date" name="due_date" title="Data de Vencimento (Obrigatório)">
        <input type="text" name="assignee" placeholder="Quem vai fazer? (Obrigatório)" autocomplete="off">
    </div>
    
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                
                <!--  MODIFICADO: Bloco de conteúdo expandido -->
                <div class="task-content">
                    <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                    
                    <!--  ADICIONADO: Mostra a descrição apenas se ela não estiver vazia -->
                    <?php if (!empty($task['description'])): ?>
                        <span class="task-desc"><?php echo htmlspecialchars($task['description']); ?></span>
                    <?php endif; ?>
                    
                    <!--  ADICIONADO: Meta-informações formatadas -->
                    <div class="task-meta">
                         <strong>Vence em:</strong> <?php echo date('d/m/Y', strtotime($task['due_date'])); ?> | 
                         <strong>Responsável:</strong> <?php echo htmlspecialchars($task['assignee']); ?>
                    </div>
                </div>

                <!-- COMO DEVE FICAR (Corrigido): -->
                <div class="actions"> 
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>

</div>

</body>
</html>