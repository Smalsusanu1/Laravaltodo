<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; background-color: #f4f4f4; }
        h1 { text-align: center; color: #333; }
        .task-form { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .task-form input, .task-form textarea { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; }
        .task-form button { background-color: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        .task-list { list-style: none; padding: 0; }
        .task-item { background: #fff; padding: 10px; margin: 5px 0; border-radius: 5px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .task-item.completed span { text-decoration: line-through; color: #888; }
        .task-item button { background-color: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>To-Do List</h1>
    <div class="task-form">
        <input type="text" id="title" placeholder="Task Title" required>
        <textarea id="description" placeholder="Task Description"></textarea>
        <button onclick="addTask()">Add Task</button>
    </div>
    <ul class="task-list" id="taskList"></ul>
    <p id="error" style="color: red; text-align: center;"></p>
    <script>
        const apiUrl = 'http://127.0.0.1:8000/api/tasks';

        function loadTasks() {
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to load tasks');
                    return response.json();
                })
                .then(tasks => {
                    const taskList = document.getElementById('taskList');
                    taskList.innerHTML = tasks.length ? '' : '<li>No tasks available.</li>';
                    tasks.forEach(task => {
                        const li = document.createElement('li');
                        li.className = `task-item ${task.completed ? 'completed' : ''}`;
                        li.innerHTML = `
                            <input type="checkbox" ${task.completed ? 'checked' : ''} onchange="toggleTask(${task.id}, this.checked)">
                            <span>${task.title} - ${task.description || 'No description'}</span>
                            <button onclick="deleteTask(${task.id})">Delete</button>
                        `;
                        taskList.appendChild(li);
                    });
                })
                .catch(err => document.getElementById('error').textContent = err.message);
        }

        function addTask() {
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;
            if (!title) { alert('Title is required!'); return; }
            fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, description })
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to add task');
                return response.json();
            })
            .then(() => {
                document.getElementById('title').value = '';
                document.getElementById('description').value = '';
                loadTasks();
            })
            .catch(err => document.getElementById('error').textContent = err.message);
        }

        function toggleTask(id, completed) {
            fetch(`${apiUrl}/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ completed })
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to update task');
                loadTasks();
            })
            .catch(err => document.getElementById('error').textContent = err.message);
        }

        function deleteTask(id) {
            fetch(`${apiUrl}/${id}`, {
                method: 'DELETE'
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to delete task');
                loadTasks();
            })
            .catch(err => document.getElementById('error').textContent = err.message);
        }

        window.onload = loadTasks;
    </script>
</body>
</html>