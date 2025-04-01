// Biến để theo dõi trạng thái chỉnh sửa: true nếu đang chỉnh sửa, false nếu đang thêm mới
let isEditing = false;
// Biến lưu ID của task đang được chỉnh sửa, null nếu không có task nào đang chỉnh sửa
let editingTaskId = null;

// Hàm thêm hoặc cập nhật task
function addTask() {
    // Lấy các phần tử input từ DOM bằng ID
    const titleInput = document.getElementById('taskTitle');
    const descriptionInput = document.getElementById('taskDescription');
    const categoryInput = document.getElementById('taskCategory');

    // Lấy giá trị từ các input và loại bỏ khoảng trắng thừa
    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();
    const category_id = categoryInput.value;

    // Kiểm tra xem người dùng đã nhập đầy đủ thông tin chưa
    // Nếu cả 3 trường đều rỗng, hiển thị cảnh báo và dừng hàm
    if (title === '' && description === '' && category_id === '') {
        alert('Please fill in all information!');
        return;
    }

    // Kiểm tra trạng thái chỉnh sửa để quyết định tạo mới hay cập nhật task
    if (isEditing) {
        // Nếu đang chỉnh sửa, gửi yêu cầu POST đến endpoint '?action=update'
        fetch('?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, // Định dạng dữ liệu gửi đi
            // Gửi dữ liệu bao gồm task_id, title, description và category_id
            body: `task_id=${editingTaskId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}`
        })
        .then(response => response.json()) // Chuyển phản hồi từ server thành JSON
        .then(data => {
            // Kiểm tra nếu cập nhật thành công
            if (data.success == true) {
                // Đặt lại trạng thái về thêm mới
                isEditing = false;
                editingTaskId = null;
                // Xóa nội dung trong các ô input
                titleInput.value = '';
                descriptionInput.value = '';
                categoryInput.value = '';
                // Tải lại trang để cập nhật giao diện
                window.location.reload();
            }else{
                alert(data.message);
                return;
            }
        });
    } else {
        // Nếu không phải chế độ chỉnh sửa, gửi yêu cầu POST để tạo task mới
        fetch('?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, // Định dạng dữ liệu gửi đi
            // Gửi dữ liệu bao gồm title, description và category_id
            body: `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}`
        })
        .then(response => response.json()) // Chuyển phản hồi từ server thành JSON
        .then(data => {
            // Kiểm tra nếu tạo task thành công
            if (data.success === true) {
                // Xóa nội dung trong các ô input
                titleInput.value = '';
                descriptionInput.value = '';
                categoryInput.value = '';
                // Tải lại trang để cập nhật giao diện
                window.location.reload();
            }else{
                alert(data.message);
                return;
            }
        });
    }
}

// Hàm để chỉnh sửa một task hiện có
function editTask(task_id, title, description, category_id) {
    // Điền thông tin task vào các ô input để người dùng chỉnh sửa
    document.getElementById('taskTitle').value = title;
    document.getElementById('taskDescription').value = description;
    document.getElementById('taskCategory').value = category_id || ''; // Nếu category_id không có thì để rỗng
    // Chuyển sang chế độ chỉnh sửa
    isEditing = true;
    // Lưu ID của task đang chỉnh sửa
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


function filterTasksByCategory() {
    const categoryId = document.getElementById('categoryFilter').value;
    const search = new URLSearchParams(window.location.search).get('search') || '';
    const filter = new URLSearchParams(window.location.search).get('filter') || 'incomplete';
    const sort = new URLSearchParams(window.location.search).get('sort') || 'false';

    // Cập nhật URL với category_id
    const newUrl = `?filter=${filter}&search=${encodeURIComponent(search)}&sort=${sort}&category_id=${categoryId}`;
    window.location.href = newUrl;
}