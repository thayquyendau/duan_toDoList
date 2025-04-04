<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">


</head>

<body>
    <div class="todo-container">
        <div class="todo-header">
            <h2 class="text-center mb-0">To-Do List</h2>
            <small class="d-block text-center opacity-75">Welcome, <?php echo $_SESSION['username']; ?> | <a href="?action=logout" class="text-white">Logout</a></small>
        </div>

        <div class="p-3">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="filterSelect" onchange="filterTasks()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Tasks</option>
                        <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="incomplete" <?php echo $filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <button class="btn btn-warning btn-sm" onclick="sortTasks()">
                    <i class="fas fa-sort"></i> Sort by Status
                </button>
                <button class="btn btn-danger btn-sm" onclick="clearCompleted()">
                    <i class="fas fa-trash"></i> Clear Completed
                </button>
                <a href="?action=export" class="btn btn-success btn-sm">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </a>
                <input type="file" id="importFile" style="display: none;" accept=".json" onchange="importTasks(event)">
                <button class="btn btn-info btn-sm" onclick="document.getElementById('importFile').click()">
                    <i class="fas fa-file-import"></i> Import Tasks
                </button>
            </div>
        </div>

        <div class="stats d-flex justify-content-between" id="stats">
            <div class="stats-left mt-2">
                Total: <?php echo $stats['total']; ?>
                | Completed: <?php echo $stats['completed']; ?>
                | Incomplete: <?php echo $stats['incomplete']; ?>
            </div>
            <button type="button" class="btn btn-info" onclick="openAddCategoryModal()">
                <i class="fas fa-plus"></i> Add Category
            </button>
            <button type="button" class="btn btn-primary" onclick="openAddTaskModal()" >
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>

        <!-- Danh sách task nhóm theo danh mục -->
        <!-- Danh sách task nhóm theo danh mục -->
        <div class="accordion" id="taskAccordion">
            <?php foreach ($tasksByCategory as $category_id => $categoryData): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header d-flex justify-content-between align-items-center" id="heading-<?php echo $category_id; ?>">
                        <!-- Nút sổ xuống bên trái -->
                        <button class="btn accordion-toggle me-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-<?php echo $category_id; ?>"
                            aria-expanded="false"
                            aria-controls="collapse-<?php echo $category_id; ?>">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <!-- Tên danh mục và số lượng task -->
                        <span class="category-name flex-grow-1 accordion-toggle me-2" data-bs-toggle="collapse"
                            data-bs-target="#collapse-<?php echo $category_id; ?>"
                            aria-expanded="false"
                            aria-controls="collapse-<?php echo $category_id; ?>">
                            <?php echo htmlspecialchars($categoryData['name']); ?>
                            (<?php echo count($categoryData['tasks']); ?> tasks)
                        </span>
                        <!-- Nút sửa và xóa nằm ngang hàng -->
                        <div class="category-actions d-flex align-items-center ">
                            <button id="category_id" class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#categoryModal"
                                onclick="editCategory(<?php echo $category_id; ?>, '<?php echo addslashes($categoryData['name']) ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(<?php echo $category_id; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </h2>
                    <div id="collapse-<?php echo $category_id; ?>"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-<?php echo $category_id; ?>">
                        <div class="accordion-body">
                            <?php if (empty($categoryData['tasks'])): ?>
                                <p class="text-muted">No tasks in this category.</p>
                            <?php else: ?>
                                <?php foreach ($categoryData['tasks'] as $task): ?>
                                    <div class="task-item <?php echo $task['status'] === 'Completed' ? 'completed' : ''; ?>"
                                        data-id="<?php echo $task['task_id']; ?>">
                                        <input type="checkbox" class="form-check-input me-2"
                                            <?php echo $task['status'] === 'Completed' ? 'checked' : ''; ?>
                                            onchange="toggleTask(<?php echo $task['task_id']; ?>, this.checked)">
                                        <span class="flex-grow-1">
                                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                                            <small class="d-block text-muted"><?php echo htmlspecialchars($task['description']); ?></small>
                                        </span>
                                        <button class="btn btn-warning btn-sm btn-action me-2"
                                            data-bs-toggle="modal" data-bs-target="#taskModal"
                                            onclick="editTask(<?php echo $task['task_id']; ?>, '<?php echo addslashes($task['title']); ?>', '<?php echo addslashes($task['description']); ?>', '<?php echo $task['category_id'] ?: ''; ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-action"
                                            onclick="deleteTask(<?php echo $task['task_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal thêm, sửa nhiệm vụ-->
        <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="">
                        <!-- Form nhập liệu -->
                        <div class="input-group">
                            <div class="row w-100">
                                <div class="col-md-4 mb-2">
                                    <input type="text" class="form-control" id="taskTitle" placeholder="Task title...">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <input type="text" class="form-control" id="taskDescription" placeholder="Description...">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select" id="taskCategory">
                                        <option value="">No Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="datetime-local" class="form-control" id="startTime">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="datetime-local" class="form-control" id="endTime">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="addTask()">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal thêm/sửa danh mục -->
        <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add / Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form nhập liệu -->
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" placeholder="Enter category name...">
                            <div id="categoryError" class="text-danger mt-2" style="display: none;"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="saveCategory()">Save</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Thêm / sửa nhiệm vụ trực tiếp -->
            <!-- <div class="input-group">   
            <div class="row w-100">
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" id="taskTitle" placeholder="Task title...">
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" id="taskDescription" placeholder="Description...">
                </div>
                <div class="col-md-2 mb-2">
                    <select class="form-select" id="taskCategory">
                        <option value="">No Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="addTask()">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </div>
        </div> -->
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="js/script.js"></script>
</body>

</html>