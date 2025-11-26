<?php
session_start();

// For testing: Check if already logged in, otherwise set test session
if (!isset($_SESSION['user_id'])) {
    // Simulate clinic manager login for testing
    $_SESSION['user_id'] = 3;
    $_SESSION['role'] = 'clinic_manager';
    $_SESSION['username'] = 'cm_test';
    $_SESSION['full_name'] = 'Test Clinic Manager';
}

// Debug: Show current session
$sessionDebug = [
    'user_id' => $_SESSION['user_id'] ?? 'NOT SET',
    'role' => $_SESSION['role'] ?? 'NOT SET',
    'username' => $_SESSION['username'] ?? 'NOT SET'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Product Image Upload</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-label {
            background: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-input-label:hover {
            background: #e8e8e8;
            border-color: #667eea;
        }
        input[type="file"] {
            display: none;
        }
        .image-preview {
            margin-top: 15px;
            text-align: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .response {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .response.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .response.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }
        .session-debug {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
        }
        .session-debug code {
            background: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Test Product Image Upload</h1>
        <p class="subtitle">Upload images to the server (max 5MB, JPG/PNG/GIF/WebP)</p>
        
        <div class="session-debug">
            <strong>🔐 Session Status:</strong>
            User ID: <code><?php echo htmlspecialchars($sessionDebug['user_id']); ?></code> | 
            Role: <code><?php echo htmlspecialchars($sessionDebug['role']); ?></code> | 
            Username: <code><?php echo htmlspecialchars($sessionDebug['username']); ?></code>
        </div>
        <div class="info-box">
            <strong>📁 Storage Info:</strong>
            Images will be saved to: <code>public/images/products/</code><br>
            Database stores: <code>/PETVET/public/images/products/filename.jpg</code>
        </div>

        <form id="uploadForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required placeholder="e.g., Premium Dog Food">
            </div>

            <div class="form-group">
                <label for="price">Price (PHP) *</label>
                <input type="number" id="price" name="price" step="0.01" required placeholder="e.g., 1500">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="food">Food & Treats</option>
                    <option value="toys">Toys & Games</option>
                    <option value="litter">Litter & Training</option>
                    <option value="accessories">Accessories & Supplies</option>
                    <option value="grooming">Grooming & Health</option>
                    <option value="medicine">Medicine & Health</option>
                </select>
            </div>

            <div class="form-group">
                <label>Product Image *</label>
                <div class="file-input-wrapper">
                    <label for="product_image" class="file-input-label">
                        <span id="fileLabel">📁 Click to select image (JPG, PNG, GIF, WebP)</span>
                    </label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required>
                </div>
                <div id="imagePreview" class="image-preview"></div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Optional product description"></textarea>
            </div>

            <div class="form-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" value="10" placeholder="e.g., 10">
            </div>

            <div class="form-group">
                <label for="seller">Seller</label>
                <input type="text" id="seller" name="seller" value="PetVet Store" placeholder="e.g., PetVet Store">
            </div>

            <button type="submit" id="submitBtn">Upload Product with Image</button>
        </form>

        <div id="response" class="response"></div>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const fileInput = document.getElementById('product_image');
        const fileLabel = document.getElementById('fileLabel');
        const imagePreview = document.getElementById('imagePreview');
        const submitBtn = document.getElementById('submitBtn');
        const responseDiv = document.getElementById('response');

        // Image preview
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileLabel.textContent = `✅ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                fileLabel.textContent = '📁 Click to select image (JPG, PNG, GIF, WebP)';
                imagePreview.innerHTML = '';
            }
        });

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Uploading...';
            responseDiv.style.display = 'none';

            const formData = new FormData(form);

            try {
                // Use test API endpoint (no auth check)
                const response = await fetch('/PETVET/api/products/add-test.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                responseDiv.style.display = 'block';
                responseDiv.className = `response ${result.success ? 'success' : 'error'}`;
                responseDiv.textContent = result.message;

                if (result.success) {
                    form.reset();
                    fileLabel.textContent = '📁 Click to select image (JPG, PNG, GIF, WebP)';
                    imagePreview.innerHTML = '';
                    
                    setTimeout(() => {
                        window.location.href = '/PETVET/test-products.php';
                    }, 2000);
                }

            } catch (error) {
                responseDiv.style.display = 'block';
                responseDiv.className = 'response error';
                responseDiv.textContent = 'Error: ' + error.message;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upload Product with Image';
            }
        });
    </script>
</body>
</html>
