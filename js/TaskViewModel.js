// TaskViewModel.js - O cérebro do Front-end
class TaskViewModel {
    constructor() {
        this.tasks = [];
        this.init();
    }

    async init() {
        this.listElement = document.getElementById('taskList');
        this.titleInput = document.getElementById('taskTitle');
        this.descInput = document.getElementById('taskDescription');
        this.dueDateInput = document.getElementById('taskDueDate');
        this.assigneeInput = document.getElementById('taskAssignee');
        this.addBtn = document.getElementById('addBtn');

        this.addBtn.onclick = () => this.addTask();
        await this.fetchTasks();
    }

    async fetchTasks() {
        const response = await fetch('api.php?action=list');
        this.tasks = await response.json();
        this.render();
    }

    async addTask() {
        const title = this.titleInput.value;
        const description = this.descInput.value;
        const due_date = this.dueDateInput.value;
        const assignee = this.assigneeInput.value;

        if (!title || !assignee) {
            return alert("Título e Responsável são obrigatórios!");
        }

        await fetch('api.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description, due_date, assignee })
        });

        this.titleInput.value = '';
        this.descInput.value = '';
        this.dueDateInput.value = '';
        this.assigneeInput.value = '';

        await this.fetchTasks();
    }

    async completeTask(id) {
        await fetch(`api.php?action=complete&id=${id}`);
        await this.fetchTasks();
    }

    async deleteTask(id) {
        await fetch(`api.php?action=delete&id=${id}`);
        await this.fetchTasks();
    }

    render() {
        this.listElement.innerHTML = '';
        this.tasks.forEach(task => {
            const li = document.createElement('li');
            li.className = task.done ? 'done' : '';
            li.innerHTML = `
                <div>
                    <strong style="${task.done ? 'text-decoration: line-through; color: #888;' : ''}">${task.title}</strong>
                    <p>${task.description || ''}</p>
                    <small>Vence em: ${task.due_date || 'N/A'} | Responsável: ${task.assignee}</small>
                </div>
                <div style="display: flex; flex-direction: row; gap: 5px; align-items: center;">
                    <button onclick="vm.completeTask(${task.id})" title="Concluir">✅</button>
                    <button onclick="vm.deleteTask(${task.id})" title="Eliminar">❌</button>
                </div>
            `;
            this.listElement.appendChild(li);
        });
    }
}

const vm = new TaskViewModel();