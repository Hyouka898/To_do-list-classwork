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


                <div class="mb-3 " >
                    <input class="form-check-input shadow-none border-sm" type="checkbox" value="" id="defaultCheck1">
                    <label class="form-check-label" for="defaultCheck1">
                        Status
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    document.getElementById('productId').value = '';
    document.getElementById('previewImage').src = '';
    document.getElementById('defaultCheck1').checked = true; // Default status = 1
});

document.getElementById('closeDrawer').addEventListener('click', () => {
    drawer.classList.remove('open');
});

// Load Products
function loadProducts() {
    $.ajax({
        url: 'api.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            let rows = '';
            data.forEach((product, index) => {
                rows += `
                    <tr id="row_${product.id}">
                        <td>${product.id}</td>
                        <td>${product.title}</td>
                        <td><img src="uploads/${product.image}" class="product-image"></td>
                        <td><span class="badge ${product.status == 1 ? 'bg-success' : 'bg-danger'}">${product.status == 1 ? 'Done' : 'Pending'}</span></td>
                        <td><span class="badge bg-${product.status == 1 ? 'success' : 'danger'}">${product.status == 1 ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id})"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-info btn-sm text-white" onclick="viewProduct(${product.id})"><i class="fa-solid fa-calendar-week"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
            });
            $('#productTable').html(rows);
        }
    });
}

// Save (Insert/Update)
$('#productForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let id = $('#productId').val();
    let status = $('#defaultCheck1').is(':checked') ? 1 : 0;
    formData.append('status', status);

    $.ajax({
        url: 'api.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                Swal.fire('Error', response.error, 'error');
                return;
            }
            drawer.classList.remove('open');
            if (id) {
                // Update row
                $(`#row_${id}`).replaceWith(`
                    <tr id="row_${response.id}">
                        <td>${response.id}</td>
                        <td>${response.title}</td>
                        <td><img src="uploads/${response.image}" class="product-image"></td>
                        <td><span class="badge ${response.status == 1 ? 'bg-success' : 'bg-danger'}">${response.status == 1 ? 'Done' : 'Pending'}</span></td>
                        <td><span class="badge bg-${response.status == 1 ? 'success' : 'danger'}">${response.status == 1 ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editProduct(${response.id})"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-info btn-sm text-white" onclick="viewProduct(${response.id})"><i class="fa-solid fa-calendar-week"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${response.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`);
                Swal.fire('Success', 'Product updated successfully!', 'success');
            } else {
                // Add new row
                $('#productTable').prepend(`
                    <tr id="row_${response.id}">
                        <td>${response.id}</td>
                        <td>${response.title}</td>
                        <td><img src="uploads/${response.image}" class="product-image"></td>
                        <td><span class="badge ${response.status == 1 ? 'bg-success' : 'bg-danger'}">${response.status == 1 ? 'Done' : 'Pending'}</span></td>
                        <td><span class="badge bg-${response.status == 1 ? 'success' : 'danger'}">${response.status == 1 ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editProduct(${response.id})"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-info btn-sm text-white" onclick="viewProduct(${response.id})"><i class="fa-solid fa-calendar-week"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${response.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`);
                Swal.fire('Success', 'Product added successfully!', 'success');
            }
        },
        error: function() {
            Swal.fire('Error', 'Something went wrong!', 'error');
        }
    });
});

// Edit Product
function editProduct(id) {
    $.ajax({
        url: 'api.php',
        method: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            let product = data.find(p => p.id == id);
            if (product) {
                $('#productId').val(product.id);
                $('#productCode').val(product.id); // Assuming code is ID for simplicity
                $('#productName').val(product.title);
                $('#defaultCheck1').prop('checked', product.status == 1);
                $('#previewImage').attr('src', 'uploads/' + product.image);
                drawer.classList.add('open');
            }
        }
    });
}

// View Product (just open drawer with disabled inputs if needed)
function viewProduct(id) {
    editProduct(id); // Reuse editProduct for simplicity
    $('#productForm input, #productForm button[type="submit"]').prop('disabled', true);
    setTimeout(() => $('#productForm input, #productForm button[type="submit"]').prop('disabled', false), 1000);
}

// Delete Product
function deleteProduct(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'api.php?id=' + id,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $(`#row_${id}`).remove();
                        Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                }
            });
        }
    });
}

loadProducts();
</script>

</body>
</html>
