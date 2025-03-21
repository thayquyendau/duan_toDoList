let isEditing = false;
let editingTaskId = null;

function addTask() {
    const titleInput = document.getElementById('taskTitle');
    const descriptionInput = document.getElementById('taskDescription');
    const categoryInput = document.getElementById('taskCategory');
    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();
    const category_id = categoryInput.value;

    if (title === '') {
        alert('Please enter a task title!');
        return;
    }

    if (isEditing) {
        fetch('?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `task_id=${editingTaskId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isEditing = false;
                editingTaskId = null;
                titleInput.value = '';
                descriptionInput.value = '';
                categoryInput.value = '';
                window.location.reload();
            }
        });
    } else {
        fetch('?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                titleInput.value = '';
                descriptionInput.value = '';
                categoryInput.value = '';
                window.location.reload();
            }
        });
    }
}

function editTask(task_id, title, description, category_id) {
    document.getElementById('taskTitle').value = title;
    document.getElementById('taskDescription').value = description;
    document.getElementById('taskCategory').value = category_id || '';
    isEditing = true;
    editingTaskId = task_id;
}

function deleteTask(task_id) {
    fetch('?action=delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `task_id=${task_id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}

function toggleTask(task_id, completed) {
    const status = completed ? 'Completed' : 'Pending';
    fetch('?action=toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `task_id=${task_id}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}

function clearCompleted() {
    fetch('?action=clear_completed', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: ''
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}

function sortTasks() {
    const filter = document.getElementById('filterSelect').value;
    const search = document.getElementById('searchInput').value;
    window.location.href = `?filter=${filter}&search=${search}&sort=true`;
}

function filterTasks() {
    const filter = document.getElementById('filterSelect').value;
    const search = document.getElementById('searchInput').value;
    window.location.href = `?filter=${filter}&search=${search}`;
}

function searchTasks() {
    const filter = document.getElementById('filterSelect').value;
    const search = document.getElementById('searchInput').value;
    window.location.href = `?filter=${filter}&search=${search}`;
}

function importTasks(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const importedTasks = JSON.parse(e.target.result);
                importedTasks.forEach(task => {
                    fetch('?action=create', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `title=${encodeURIComponent(task.title)}&description=${encodeURIComponent(task.description)}&category_id=${task.category_id || ''}`
                    });
                });
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } catch (error) {
                alert('Error importing tasks. Please ensure the file is a valid JSON.');
            }
        };
        reader.readAsText(file);
    }
}

document.getElementById('taskTitle').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        addTask();
    }
});

document.getElementById('searchInput').addEventListener('input', searchTasks);