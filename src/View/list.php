<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master (MVC Edition)</title>
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
        .form-vertical { 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
            margin-top: 20px; 
            margin-bottom: 20px; 
        }
        .form-row { 
            display: flex; 
            gap: 10px; 
        }
        .form-row input { 
            flex: 1; 
            width: 100%;
        }
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
    <h1>Task Master (MVC Edition)</h1>

    <?php if (isset($error) && !empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulário apontando para a action 'create' -->
    <form method="POST" action="index.php?action=create" class="form-vertical">
        <input type="text" name="title" placeholder="O que precisa ser feito? (Obrigatório)" autocomplete="off" required>
        
        <textarea name="description" placeholder="Descrição da tarefa... (Opcional)" rows="2"></textarea>
        
        <div class="form-row">
            <input type="date" name="due_date" title="Data de Vencimento (Obrigatório)" required>
            <input type="text" name="assignee" placeholder="Quem vai fazer? (Obrigatório)" autocomplete="off" required>
        </div>
        
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div class="task-content">
                    <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                    
                    <?php if (!empty($task['description'])): ?>
                        <span class="task-desc"><?php echo htmlspecialchars($task['description']); ?></span>
                    <?php endif; ?>
                    
                    <div class="task-meta">
                         <strong>Vence em:</strong> <?php echo !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : ''; ?> | 
                         <strong>Responsável:</strong> <?php echo htmlspecialchars($task['assignee'] ?? ''); ?>
                    </div>
                </div>

                <div class="actions"> 
                    <?php if (!$task['done']): ?>
                        <a href="index.php?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                    <?php endif; ?>
                    <a href="index.php?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>