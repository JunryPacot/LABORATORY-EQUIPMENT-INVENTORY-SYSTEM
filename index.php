<?php
// LABORATORY EQUIPMENT INVENTORY SYSTEM
// Main Application File (CRUD & Search/Filter Interface)

require_once 'config.php';

$message = '';
$message_type = '';

// 1. HANDLE DELETE ACTION
if (isset($_GET['delete'])) {
    $delete_id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id = ?");
        $stmt->execute([$delete_id]);
        $message = "Equipment record '$delete_id' deleted successfully.";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting equipment: " . $e->getMessage();
        $message_type = "error";
    }
}

// 2. HANDLE ADD ACTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_equipment'])) {
    $equipment_id   = trim($_POST['equipment_id']);
    $equipment_name = trim($_POST['equipment_name']);
    $category       = trim($_POST['category']);
    $quantity       = (int)$_POST['quantity'];
    $condition      = trim($_POST['condition']);
    $laboratory     = trim($_POST['laboratory']);
    $date_acquired  = trim($_POST['date_acquired']);

    if (empty($equipment_id) || empty($equipment_name) || empty($category) || empty($condition) || empty($laboratory) || empty($date_acquired)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } else {
        try {
            // Check duplicate ID
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM equipment WHERE equipment_id = ?");
            $checkStmt->execute([$equipment_id]);
            if ($checkStmt->fetchColumn() > 0) {
                $message = "Equipment ID '$equipment_id' already exists in the system.";
                $message_type = "error";
            } else {
                $stmt = $pdo->prepare("INSERT INTO equipment (equipment_id, equipment_name, category, quantity, `condition`, laboratory, date_acquired) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$equipment_id, $equipment_name, $category, $quantity, $condition, $laboratory, $date_acquired]);
                $message = "New equipment record added successfully.";
                $message_type = "success";
            }
        } catch (PDOException $e) {
            $message = "Error adding record: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// 3. HANDLE UPDATE ACTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_equipment'])) {
    $equipment_id   = trim($_POST['equipment_id']);
    $equipment_name = trim($_POST['equipment_name']);
    $category       = trim($_POST['category']);
    $quantity       = (int)$_POST['quantity'];
    $condition      = trim($_POST['condition']);
    $laboratory     = trim($_POST['laboratory']);
    $date_acquired  = trim($_POST['date_acquired']);

    if (empty($equipment_id) || empty($equipment_name) || empty($category) || empty($condition) || empty($laboratory) || empty($date_acquired)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE equipment SET equipment_name = ?, category = ?, quantity = ?, `condition` = ?, laboratory = ?, date_acquired = ? WHERE equipment_id = ?");
            $stmt->execute([$equipment_name, $category, $quantity, $condition, $laboratory, $date_acquired, $equipment_id]);
            $message = "Equipment record '$equipment_id' updated successfully.";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error updating record: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// 4. FETCH EDIT DATA IF IN EDIT MODE
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = trim($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM equipment WHERE equipment_id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch();
}

// 5. FETCH RECORDS WITH SEARCH & FILTER
$search           = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_category  = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_condition = isset($_GET['condition']) ? trim($_GET['condition']) : '';

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(equipment_id LIKE ? OR equipment_name LIKE ? OR `condition` LIKE ? OR category LIKE ? OR laboratory LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_category)) {
    $where[] = "category = ?";
    $params[] = $filter_category;
}

$sql = "SELECT * FROM equipment";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY equipment_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();

// Categories & Conditions List
$categories = ['Computer', 'Electronics', 'Audio Visual', 'Office Equipment', 'Laboratory Equipment', 'Other'];
$conditions = ['Good', 'For Repair', 'Damaged', 'Unserviceable'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LABORATORY EQUIPMENT INVENTORY SYSTEM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- Header -->
    <header class="header">
        <h1>LABORATORY EQUIPMENT INVENTORY SYSTEM</h1>
        <p>Systems Analysis and Design Laboratory Activity</p>
    </header>

    <!-- Notification Message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Main Layout Grid -->
    <div class="layout-grid">
        
        <!-- Left: Equipment Entry / Edit Form -->
        <div class="panel form-panel">
            <h2><?= $edit_data ? 'Edit Equipment' : 'Add New Equipment' ?></h2>
            <form action="index.php" method="POST">
                
                <div class="form-group">
                    <label for="equipment_id">Equipment ID: <span class="required">*</span></label>
                    <input type="text" id="equipment_id" name="equipment_id" value="<?= htmlspecialchars($edit_data['equipment_id'] ?? '') ?>" <?= $edit_data ? 'readonly' : 'required' ?> placeholder="e.g. EQ001">
                </div>

                <div class="form-group">
                    <label for="equipment_name">Equipment Name: <span class="required">*</span></label>
                    <input type="text" id="equipment_name" name="equipment_name" value="<?= htmlspecialchars($edit_data['equipment_name'] ?? '') ?>" required placeholder="e.g. Desktop Computer">
                </div>

                <div class="form-group">
                    <label for="category">Category: <span class="required">*</span></label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($edit_data['category']) && $edit_data['category'] === $cat) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity: <span class="required">*</span></label>
                    <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($edit_data['quantity'] ?? '1') ?>" min="0" required>
                </div>

                <div class="form-group">
                    <label for="condition">Condition: <span class="required">*</span></label>
                    <select id="condition" name="condition" required>
                        <option value="">-- Select Condition --</option>
                        <?php foreach ($conditions as $cond): ?>
                            <option value="<?= htmlspecialchars($cond) ?>" <?= (isset($edit_data['condition']) && $edit_data['condition'] === $cond) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cond) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="laboratory">Laboratory: <span class="required">*</span></label>
                    <input type="text" id="laboratory" name="laboratory" value="<?= htmlspecialchars($edit_data['laboratory'] ?? '') ?>" required placeholder="e.g. Computer Lab 1">
                </div>

                <div class="form-group">
                    <label for="date_acquired">Date Acquired: <span class="required">*</span></label>
                    <input type="date" id="date_acquired" name="date_acquired" value="<?= htmlspecialchars($edit_data['date_acquired'] ?? date('Y-m-d')) ?>" required>
                </div>

                <div class="form-buttons">
                    <?php if ($edit_data): ?>
                        <button type="submit" name="update_equipment" class="btn btn-primary">UPDATE</button>
                        <a href="index.php" class="btn btn-secondary">CANCEL</a>
                    <?php else: ?>
                        <button type="submit" name="add_equipment" class="btn btn-primary">SAVE</button>
                        <button type="reset" class="btn btn-secondary">CLEAR</button>
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <!-- Right: Equipment Records & Search -->
        <div class="panel table-panel">
            <h2>Equipment Inventory Records</h2>

            <!-- Search & Filter Bar -->
            <form action="index.php" method="GET" class="search-form">
                <div class="search-row">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search ID, Name, Condition, or Lab...">
                    
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= ($filter_category === $cat) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-search">SEARCH</button>
                    <a href="index.php" class="btn btn-reset">RESET</a>
                </div>
            </form>

            <!-- Table -->
            <div class="table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Equipment ID</th>
                            <th>Equipment Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Condition</th>
                            <th>Laboratory</th>
                            <th>Date Acquired</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($records) > 0): ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['equipment_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td class="text-center"><?= (int)$row['quantity'] ?></td>
                                    <td><?= htmlspecialchars($row['condition']) ?></td>
                                    <td><?= htmlspecialchars($row['laboratory']) ?></td>
                                    <td><?= htmlspecialchars($row['date_acquired']) ?></td>
                                    <td class="action-cell">
                                        <a href="index.php?edit=<?= urlencode($row['equipment_id']) ?>" class="btn-action edit">Edit</a>
                                        <a href="index.php?delete=<?= urlencode($row['equipment_id']) ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this equipment record?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-records">No equipment records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="record-summary">
                Total Equipment Types Found: <strong><?= count($records) ?></strong>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Laboratory Equipment Inventory System - Laboratory Activity</p>
    </footer>

</div>

</body>
</html>
