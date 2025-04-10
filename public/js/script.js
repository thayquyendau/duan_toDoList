
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
        alert('Vui lòng điền tên danh mục!');
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

// GỌI KHI BẤM "LƯU" TRONG FORM
function addTask() {
    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();
    const category_id = categoryInput.value;
    const startTime = startTimeInput.value;
    const endTime = endTimeInput.value;

    console.log("addTask called with isEditing:", isEditing, "editingTaskId:", editingTaskId);
    console.log("Form values:", { title, description, category_id, startTime, endTime });

    // Validate input
    if (!title || !description || !category_id) {
        console.log("Validation failed: Missing title, description, or category_id");
        alert("Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    if (!startTime || !endTime) {
        console.log("Validation failed: Missing startTime or endTime");
        alert("Vui lòng nhập thời gian bắt đầu và kết thúc!");
        return;
    }

    const startDate = new Date(startTime);
    const endDate = new Date(endTime);
    const now = new Date();
    console.log("Dates:", { startDate, endDate, now });

    if (startDate < now) {
        console.log("Validation failed: startDate is in the past");
        alert("Thời gian bắt đầu phải lớn hơn thời gian hiện tại!");
        return;
    }

    if (endDate <= startDate) {
        console.log("Validation failed: endDate is not after startDate");
        alert("Thời gian kết thúc phải sau thời gian bắt đầu!");
        return;
    }

    const startFormatted = formatDateToDB(startDate);
    const endFormatted = formatDateToDB(endDate);
    console.log("Formatted dates:", { startFormatted, endFormatted });

    const url = isEditing ? '?action=updateTask' : '?action=createTask';
    const body = isEditing
        ? `task_id=${editingTaskId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}&start_time=${startFormatted}&end_time=${endFormatted}`
        : `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&category_id=${category_id}&start_time=${startFormatted}&end_time=${endFormatted}`;
    console.log("Sending fetch request:", { url, body });

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
        .then(response => {
            console.log("Fetch response:", response);
            return response.json();
        })
        .then(data => {
            console.log("Server response data:", data);
            if (data.success) {
                // Reset form
                titleInput.value = '';
                descriptionInput.value = '';
                categoryInput.value = '';
                startTimeInput.value = '';
                endTimeInput.value = '';
                isEditing = false;
                editingTaskId = null;
                $('#taskModal').modal('hide');
                window.location.reload();
            } else {
                alert(data.message || 'Đã xảy ra lỗi!');
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert('Lỗi kết nối đến server!');
        });
}

// GỌI KHI NHẤN NÚT "SỬA"
function editTask(task_id, title, description, category_id, start_time, end_time) {
    console.log("Starting editTask with:", { task_id, title, description, category_id, start_time, end_time });
    isEditing = true;
    editingTaskId = task_id;
    // console.log("isEditing:", isEditing, "editingTaskId:", editingTaskId);

    // Điền thông tin vào các ô input
    titleInput.value = title || '';
    descriptionInput.value = description || '';
    categoryInput.value = category_id || '';
    startTimeInput.value = formatDateToLocal(start_time);
    endTimeInput.value = formatDateToLocal(end_time);
    console.log("Form values after population:", {
        title: titleInput.value,
        description: descriptionInput.value,
        category: categoryInput.value,
        startTime: startTimeInput.value,
        endTime: endTimeInput.value
    }); 

    document.getElementById('exampleModalLabel').innerText = 'Cập nhật Task';
    $('#taskModal').modal('show');
}


function openAddTaskModal() {
    // Đặt lại trạng thái
    isEditing = false;
    editingId = null;

    // Xóa nội dung input
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskCategory').value = '';
    document.getElementById('startTime').value = '';
    document.getElementById('endTime').value = '';
    // Cập nhật tiêu đề modal
    document.getElementById('exampleModalLabel').innerText = 'Add Task';
    // Mở modal
    $('#taskModal').modal('show');
}

function deleteTask(task_id) {
    fetch('?action=deleteTask', {
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

// Hàm xử lí thời gian
document.querySelectorAll(".countdown").forEach((countdownEl) => {
    const endTimeStr = countdownEl.dataset.endTime;
    const endTime = new Date(endTimeStr).getTime();

    let intervalId; // ✅ Khai báo biến ở đây trước khi dùng trong hàm bên dưới

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            countdownEl.innerText = "Đã kết thúc";
            clearInterval(intervalId); // ✅ Bây giờ không còn lỗi nữa
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownEl.innerText = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        countdownEl.style.color = "red";
    }

    updateCountdown();
    intervalId = setInterval(updateCountdown, 1000); // ✅ Gán sau khi khai báo
});
