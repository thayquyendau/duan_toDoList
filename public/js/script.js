let isEditingCategory = false;
let editingCategoryId = null;

function saveCategory() {
    const categoryNameInput = document.getElementById('categoryName');
    const categoryName = categoryNameInput.value.trim();

    if (categoryName === '') {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Vui lòng điền tên danh mục!'
        });
        return false;
    }

    const url = isEditingCategory ? '?action=updateCategory' : '?action=createCategory';
    const body = isEditingCategory ? `id=${editingCategoryId}&name=${encodeURIComponent(categoryName)}` : `name=${encodeURIComponent(categoryName)}`;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: isEditingCategory ? 'Cập nhật thành công' : 'Thêm thành công',
                    text: isEditingCategory ? 'Danh mục đã được cập nhật.' : 'Danh mục đã được thêm.'
                }).then(() => {
                    isEditingCategory = false;
                    editingCategoryId = null;
                    categoryNameInput.value = '';
                    $('#categoryModal').modal('hide');
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.message || 'Đã xảy ra lỗi!'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Lỗi kết nối đến server!'
            });
        });
}

function editCategory(categoryId, categoryName) {
    document.getElementById('categoryName').value = categoryName;
    isEditingCategory = true;
    editingCategoryId = categoryId;
    document.getElementById('categoryModalLabel').innerText = 'Edit Category';
    $('#categoryModal').modal('show');
}

function openAddCategoryModal() {
    isEditingCategory = false;
    editingCategoryId = null;
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryModalLabel').innerText = 'Add Category';
    $('#categoryModal').modal('show');
}

function deleteCategory(categoryId) {
    Swal.fire({
        title: 'Bạn có chắc không?',
        text: 'Danh mục và tất cả task liên quan sẽ bị xóa!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=deleteCategory', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${categoryId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Xóa thành công',
                            text: 'Danh mục đã được xóa.'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã xảy ra lỗi!'
                        });
                    }
                });
        }
    });
}

let isEditing = false;
let editingTaskId = null;

const titleInput = document.getElementById('taskTitle');
const descriptionInput = document.getElementById('taskDescription');
const categoryInput = document.getElementById('taskCategory');
const startTimeInput = document.getElementById('startTime');
const endTimeInput = document.getElementById('endTime');

function formatDateToDB(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

function formatDateToLocal(datetimeString) {
    if (!datetimeString) return "";
    const date = new Date(datetimeString);
    if (isNaN(date.getTime())) {
        console.error("Không thể chuyển đổi datetime:", datetimeString);
        return "";
    }
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function setDefaultStartTime() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    startTimeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    startTimeInput.disabled = true;
}

document.addEventListener('DOMContentLoaded', function () {
    setDefaultStartTime();
    $('#taskModal').on('hidden.bs.modal', function () {
        setDefaultStartTime();
        isEditing = false;
        editingTaskId = null;
    });
});

function addTask() {
    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();
    const category_id = categoryInput.value;
    const startTime = startTimeInput.value;
    const endTime = endTimeInput.value;

    if (!title || !description || !category_id) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Vui lòng nhập đầy đủ thông tin!'
        });
        return;
    }

    if (!startTime || !endTime) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Vui lòng nhập thời gian bắt đầu và kết thúc!'
        });
        return;
    }

    const startDate = new Date(startTime);
    const endDate = new Date(endTime);

    if (endDate <= startDate) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Thời gian kết thúc phải sau thời gian bắt đầu!'
        });
        return;
    }

    const startFormatted = formatDateToDB(startDate);
    const endFormatted = formatDateToDB(endDate);

    const url = isEditing ? '?action=updateTask' : '?action=createTask';
    const body = isEditing
        ? `task_id=${editingTaskId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}&start_time=${startFormatted}&end_time=${endFormatted}`
        : `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}&start_time=${startFormatted}&end_time=${endFormatted}`;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: isEditing ? 'Cập nhật thành công' : 'Thêm thành công',
                    text: isEditing ? 'Task đã được cập nhật.' : 'Task đã được thêm.'
                }).then(() => {
                    titleInput.value = '';
                    descriptionInput.value = '';
                    categoryInput.value = '';
                    startTimeInput.value = '';
                    endTimeInput.value = '';
                    isEditing = false;
                    editingTaskId = null;
                    $('#taskModal').modal('hide');
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.message || 'Đã xảy ra lỗi!'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Lỗi kết nối đến server!'
            });
        });
}

function editTask(task_id, title, description, category_id, start_time, end_time) {
    isEditing = true;
    editingTaskId = task_id;
    titleInput.value = title || '';
    descriptionInput.value = description || '';
    categoryInput.value = category_id || '';
    startTimeInput.value = formatDateToLocal(start_time);
    endTimeInput.value = formatDateToLocal(end_time);
    startTimeInput.disabled = true;
    document.getElementById('exampleModalLabel').innerText = 'Cập nhật Task';
    $('#taskModal').modal('show');
}

function openAddTaskModal() {
    isEditing = false;
    editingTaskId = null;
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskCategory').value = '';
    document.getElementById('endTime').value = '';
    document.getElementById('exampleModalLabel').innerText = 'Add Task';
    $('#taskModal').modal('show');
}

function deleteTask(task_id) {
    Swal.fire({
        title: 'Bạn có chắc không?',
        text: 'Task sẽ được chuyển vào thùng rác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=deleteTask', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `task_id=${task_id}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Xóa thành công',
                            text: 'Task đã được xóa.'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã xảy ra lỗi!'
                        });
                    }
                });
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
                Swal.fire({
                    icon: 'success',
                    title: 'Cập nhật thành công',
                    text: `Task đã được đánh dấu là ${status}.`
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.message || 'Đã xảy ra lỗi!'
                });
            }
        });
}

function clearCompleted() {
    Swal.fire({
        title: 'Bạn có chắc không?',
        text: 'Tất cả task đã hoàn thành sẽ bị xóa!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=clear_completed', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: ''
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Xóa thành công',
                            text: 'Tất cả task đã hoàn thành đã được xóa.'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã xảy ra lỗi!'
                        });
                    }
                });
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
        reader.onload = function (e) {
            try {
                const importedTasks = JSON.parse(e.target.result);
                importedTasks.forEach(task => {
                    fetch('?action=createTask', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `title=${encodeURIComponent(task.title)}&description=${encodeURIComponent(task.description)}&category_id=${task.category_id || ''}`
                    });
                });
                Swal.fire({
                    icon: 'success',
                    title: 'Nhập thành công',
                    text: 'Tasks đã được nhập.'
                }).then(() => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Error importing tasks. Please ensure the file is a valid JSON.'
                });
            }
        };
        reader.readAsText(file);
    }
}

document.getElementById('taskTitle').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        addTask();
    }
});

document.getElementById('searchInput').addEventListener('input', searchTasks);

function filterTasksByCategory() {
    const categoryId = document.getElementById('categoryFilter').value;
    const search = new URLSearchParams(window.location.search).get('search') || '';
    const filter = new URLSearchParams(window.location.search).get('filter') || 'incomplete';
    const sort = new URLSearchParams(window.location.search).get('sort') || 'false';
    const newUrl = `?filter=${filter}&search=${encodeURIComponent(search)}&sort=${sort}&category_id=${categoryId}`;
    window.location.href = newUrl;
}

document.querySelectorAll(".countdown").forEach((countdownEl) => {
    const endTimeStr = countdownEl.dataset.endTime;
    const endTime = new Date(endTimeStr).getTime();

    let intervalId;
    let notifiedOneHourLeft = false;

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            countdownEl.innerText = "Đã kết thúc";
            clearInterval(intervalId);
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        if (hours === 1 && minutes === 0 && seconds === 0 && !notifiedOneHourLeft) {
            Swal.fire({
                icon: 'warning',
                title: 'Cảnh báo',
                text: 'Còn 1 giờ nữa! Hãy chuẩn bị hoàn thành nhiệm vụ.'
            });
            notifiedOneHourLeft = true;
        }

        countdownEl.innerText = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        countdownEl.style.color = "red";
    }

    updateCountdown();
    intervalId = setInterval(updateCountdown, 1000);
});

function openHistoryModal() {
    const modal = new bootstrap.Modal(document.getElementById('historyModal'));
    modal.show();
    fetchHistoryActions();
}

function fetchHistoryActions() {
    fetch('?action=historyAction')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('historyList').innerHTML = `<p>${data.error}</p>`;
            } else {
                displayHistoryActions(data);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Không thể tải lịch sử thao tác. Vui lòng thử lại sau.'
            });
        });
}

function displayHistoryActions(actions) {
    const historyList = document.getElementById('historyList');
    historyList.innerHTML = '';

    if (actions.length === 0) {
        historyList.innerHTML = '<p>Không có lịch sử thao tác nào.</p>';
    } else {
        actions.forEach(action => {
            const actionItem = document.createElement('li');
            let restoreBtn = "";
            if (Number(action.is_deleted) === 1) {
                restoreBtn = `<span class="restore-task-btn" onclick="restoreTask(${action.task_id})" style="cursor:pointer;">🔄</span>`;
            }
            actionItem.innerHTML = `${restoreBtn} ${action.timestamp}: ${action.action} ${action.title}`;
            historyList.appendChild(actionItem);
        });
    }
}

function deleteAllAction() {
    Swal.fire({
        title: 'Bạn có chắc không?',
        text: 'Toàn bộ lịch sử sẽ bị xóa!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=deleteAllAction', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Xóa thành công',
                            text: data.message
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Lỗi khi xóa lịch sử!'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể kết nối máy chủ!'
                    });
                });
        }
    });
}

function restoreTask(task_id) {
    Swal.fire({
        title: 'Bạn có muốn khôi phục nhiệm vụ này không?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Khôi phục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=restoreTask', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `task_id=${task_id}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Khôi phục thành công',
                            text: data.message
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể kết nối máy chủ!'
                    });
                });
        }
    });
}