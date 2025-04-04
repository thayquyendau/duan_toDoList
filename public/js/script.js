// Biến để theo dõi trạng thái chỉnh sửa danh mục
let isEditingCategory = false;
// Biến lưu ID của danh mục đang được chỉnh sửa, null nếu không có danh mục nào đang chỉnh sửa
let editingCategoryId = null;

// Hàm thêm hoặc cập nhật danh mục
function saveCategory() {
    // Lấy phần tử input từ DOM bằng ID
    const categoryNameInput = document.getElementById('categoryName');
    // Lấy giá trị từ input và loại bỏ khoảng trắng thừa
    const categoryName = categoryNameInput.value.trim();

    // Kiểm tra xem người dùng đã nhập đầy đủ thông tin chưa
    if (categoryName === '') {
        alert('Please enter a category name!');
        return false;
    }

    // Xác định hành động: thêm mới hay cập nhật
    if (isEditingCategory) {
        // Nếu đang chỉnh sửa, gửi yêu cầu POST đến endpoint '?action=updateCategory'
        fetch('?action=updateCategory', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${editingCategoryId}&name=${encodeURIComponent(categoryName)}`
        })
            .then(response => response.json())
            .then(data => {
                // Kiểm tra nếu cập nhật thành công
                if (data.success === true) {
                    // Đặt lại trạng thái về thêm mới   
                    isEditingCategory = false;
                    editingCategoryId = null;
                    // Xóa nội dung trong ô input
                    categoryNameInput.value = '';
                    // Đóng modal
                    $('#categoryModal').modal('hide');
                    // Tải lại trang để cập nhật giao diện
                    window.location.reload();
                } else {
                    alert(data.message || 'Lỗi không xác định!');
                    return;
                }
            })
            .catch(error => {
                alert('Lỗi kết nối đến server!');
            });
    } else {
        // Nếu không phải chế độ chỉnh sửa, gửi yêu cầu POST để tạo danh mục mới
        fetch('?action=createCategory', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${encodeURIComponent(categoryName)}`
        })
            .then(response => response.json())
            .then(data => {
                // Kiểm tra nếu tạo danh mục thành công
                if (data.success === true) {
                    // Xóa nội dung trong ô input
                    categoryNameInput.value = '';
                    // Đóng modal
                    $('#categoryModal').modal('hide');
                    // Tải lại trang để cập nhật giao diện
                    window.location.reload();
                } else {
                    alert(data.message || 'Lỗi không xác định!');
                    return;
                }
            })
            .catch(error => {
                alert('Lỗi kết nối đến server!');
            });
    }
}

// Hàm để chỉnh sửa một danh mục hiện có
function editCategory(categoryId, categoryName) {
    // Điền thông tin danh mục vào ô input để người dùng chỉnh sửa
    document.getElementById('categoryName').value = categoryName;
    // Chuyển sang chế độ chỉnh sửa
    isEditingCategory = true;
    // Lưu ID của danh mục đang chỉnh sửa
    editingCategoryId = categoryId;
    // Cập nhật tiêu đề modal
    document.getElementById('categoryModalLabel').innerText = 'Edit Category';
    // Mở modal
    $('#categoryModal').modal('show');
}

// Hàm mở modal để thêm danh mục mới
function openAddCategoryModal() {
    // Đặt lại trạng thái
    isEditingCategory = false;
    editingCategoryId = null;
    
    // Xóa nội dung input
    document.getElementById('categoryName').value = '';
    // Cập nhật tiêu đề modal
    document.getElementById('categoryModalLabel').innerText = 'Add Category';
    // Mở modal
    $('#categoryModal').modal('show');
}
    
function deleteCategory(categoryId) {
    fetch('?action=deleteCategory', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${categoryId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
}


// Hàm thêm hoặc cập nhật task
// Biến để theo dõi trạng thái chỉnh sửa: true nếu đang chỉnh sửa, false nếu đang thêm mới
let isEditing = false;
// Biến lưu ID của task đang được chỉnh sửa, null nếu không có task nào đang chỉnh sửa
let editingTaskId = null;
function addTask() {
    // Lấy các phần tử input từ DOM bằng ID
    const titleInput = document.getElementById('taskTitle');
    const descriptionInput = document.getElementById('taskDescription');
    const categoryInput = document.getElementById('taskCategory');
    const startTimeInput = document.getElementById('startTime');
    const endTimeInput = document.getElementById('endTime');


    // Lấy giá trị từ các input và loại bỏ khoảng trắng thừa
    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();
    const category_id = categoryInput.value;
    const startTime = startTimeInput.value;
    const endTime = endTimeInput.value;


    // Kiểm tra xem người dùng đã nhập đầy đủ thông tin chưa
    // Nếu cả 5 trường đều rỗng, hiển thị cảnh báo và dừng hàm
    if (title === '' && description === '' && category_id === '') {
        alert('Please fill in all information!');
        return false;
    } else if (!startTime || !endTime) {
        alert('Vui lòng nhập cả thời gian bắt đầu và kết thúc!');
        return false;
    }
    const startDateTime = new Date(startTime);
    const endDateTime = new Date(endTime);
    const now = new Date();
    if (startDateTime < now) {
        alert('Thời gian bắt đầu phải nằm trong tương lai!');
        console.log(startDateTime);
        return false;
    }else if (endDateTime <= startDateTime) {
        alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
        console.log(endDateTime);
        return false;
    }

    // Kiểm tra trạng thái chỉnh sửa để quyết định tạo mới hay cập nhật task
    if (isEditing) {
        // Nếu đang chỉnh sửa, gửi yêu cầu POST đến endpoint '?action=update'
        fetch('?action=updateTask', {
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
                } else {
                    alert(data.message);
                    return;
                }
            });
    } else {
        // Nếu không phải chế độ chỉnh sửa, gửi yêu cầu POST để tạo task mới
        fetch('?action=createTask', {
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
                } else {
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
    document.getElementById('taskCategory').value = category_id || '';
    document.getElementById('exampleModalLabel').innerText = 'Edit Task'; // Nếu category_id không có thì để rỗng
    // Chuyển sang chế độ chỉnh sửa
    isEditing = true;
    // Lưu ID của task đang chỉnh sửa
    editingTaskId = task_id;
}

function openAddTaskModal() {
    // Đặt lại trạng thái
    isEditing = false;
    editingId = null;
    
    // Xóa nội dung input
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskCategory').value = '';
    // Cập nhật tiêu đề modal
    document.getElementById('exampleModalLabel').innerText = 'Add Task';
    // Mở modal
    $('#taskModal').modal('show');
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
        reader.onload = function (e) {
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

    // Cập nhật URL với category_id
    const newUrl = `?filter=${filter}&search=${encodeURIComponent(search)}&sort=${sort}&category_id=${categoryId}`;
    window.location.href = newUrl;
}