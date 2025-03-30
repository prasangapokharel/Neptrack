<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Workout - Admin Panel</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .admin-header h1 {
            font-size: 24px;
            margin: 0;
            margin-left: 10px;
        }
        .admin-header .icon {
            font-size: 24px;
        }
        .back-link {
            display: flex;
            align-items: center;
            color: #ccc;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-link:hover {
            color: #fff;
        }
        .form-container {
            background-color: #222;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            background-color: #333;
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
        }
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #555;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #666;
        }
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #555;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .file-input-label:hover {
            background-color: #666;
        }
        .file-name {
            margin-left: 10px;
            color: #ccc;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/templates/admin_header.php'; ?>
    
    <div class="admin-container">
        <a href="/admin/workouts" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span style="margin-left: 8px;">Add New Workout</span>
        </a>
        
        <div id="alert-message" class="alert"></div>
        
        <div class="form-container">
            <form id="add-workout-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Workout Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="difficulty_level">Difficulty Level</label>
                    <select id="difficulty_level" name="difficulty_level" class="form-control">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate" selected>Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Workout Image</label>
                    <div class="file-input-container">
                        <label class="file-input-label">Choose File</label>
                        <input type="file" id="image" name="image" class="file-input" accept="image/*">
                        <span id="file-name" class="file-name">No file chosen</span>
                    </div>
                </div>
                
                <div class="btn-container">
                    <a href="/admin/workouts" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Workout</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Update file name when file is selected
        document.getElementById('image').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            document.getElementById('file-name').textContent = fileName;
        });
        
        // Form submission
        document.getElementById('add-workout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;
            
            fetch('/api/admin/workout/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Redirect to workouts page after successful addition
                    setTimeout(() => {
                        window.location.href = '/admin/workouts';
                    }, 1500);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                showAlert('An error occurred. Please try again.', 'danger');
                console.error('Error:', error);
            });
        });
        
        function showAlert(message, type) {
            const alertElement = document.getElementById('alert-message');
            alertElement.textContent = message;
            alertElement.className = 'alert';
            alertElement.classList.add(`alert-${type}`);
            alertElement.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertElement.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>