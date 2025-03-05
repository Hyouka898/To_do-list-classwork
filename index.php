<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    
    <!-- Link to Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background: #f8f9fa;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
        }
        .drawer.open {
            right: 0;
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        } 
        #searchInput {
            width: 300px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        table tr th{
            font-size : 22px;
        }
        .img-box{
            width: 80px;
            height:80px;
            border:1px solid #333;
        }
        #mark{
            width: 30px;
            background-color:red;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="search-container ">
            <h1>Product Management</h1>
            <button id="openDrawer" class="btn btn-primary">Add Product</button>
        </div>

        <!-- table -->
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Mark as done</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                <tbody>
                   
                    <tr>
                        <td>1</td>
                        <td>Jane Smith</td>
                        <td><img src="https://via.placeholder.com/50" alt="User Image" class="rounded-circle"></td>
                        <td>
                            <span class="badge bg-success">Done</span>
                        </td>
                        <td><span class="badge bg-danger">Inactive</span></td>
                        <td>
                            <button id="btnUpdate" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button id="btnViewDel" class="btn btn-info btn-sm text-white">
                                <i class="fa-solid fa-calendar-week"></i>
                            </button>
                            <button id="btnDelete" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>

                </tbody>
            </tbody>
        </table>

    </div>

    <div class="drawer" id="drawer">
        <div class="p-4">
            <h4>Add Product</h4>
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="id">
                <div class="mb-3">
                    <label class="form-label">Product Code</label>
                    <input type="text" id="productCode" name="code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" id="productName" name="name" class="form-control" required>
                </div>

                <!-- Inside Modal Body -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="productStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="mb-3 " >
                    <input class="form-check-input shadow-none border-sm" type="checkbox" value="" id="defaultCheck1">
                    <label class="form-check-label" for="defaultCheck1">
                        Default checkbox
                    </label>
                </div>
                
                <div class="mb-3 " >
                    <label class="form-label">Image</label>
                    <div class="">
                        <input type="file" id="productImage" name="image" class="form-control ">
                        <img id="previewImage" src="" class="img-thumbnail mt-2">
                    </div>
                    
                </div>
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" id="closeDrawer" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

        document.getElementById('productImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('previewImage').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        const drawer = document.getElementById('drawer');
        document.getElementById('openDrawer').addEventListener('click', () => {
            drawer.classList.add('open');
            document.getElementById('productForm').reset();
        });
        document.getElementById('closeDrawer').addEventListener('click', () => {
            drawer.classList.remove('open');
        });

        // update 
        document.getElementById('btnUpdate').addEventListener('click', () => {
            drawer.classList.add('open');
            document.getElementById('productForm').reset();
        });
        
        // view detail
        document.getElementById('btnViewDel').addEventListener('click', () => {
            drawer.classList.add('open');
            document.getElementById('productForm').reset();
        });
        

        function loadProducts(query = '') {
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { query },
                dataType: 'json',
                success: function (data) {
                    let rows = '';
                    data.forEach((product, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><img src="uploads/${product.image}" class="product-image"></td>
                                <td>${product.code}</td>
                                <td>${product.name}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                                </td>
                            </tr>`;
                    });
                    $('#productTable').html(rows);
                }
            });
        }

        loadProducts();
    </script>

</body>
</html>
