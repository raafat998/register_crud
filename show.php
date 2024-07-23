<?php
include "db_conn.php";

$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Jost', sans-serif;
            background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e);
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 1700px;
        }
        .table-container {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-x: auto; /* Added to allow horizontal scrolling */
        }
        .table thead th {
            background-color: #573b8a;
            color: #fff;
        }
        .table img {
            max-width: 100px;
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Optional: to make images look better */
        }
        .btn-custom {
            background: #573b8a;
            color: #fff;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background: #6d44b8;
        }
        @media (max-width: 768px) {
            .table-container {
                padding: 1rem;
            }
            .btn-custom {
                width: 100%;
                margin-bottom: 1rem;
            }
        }
        @media (max-width: 576px) {
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border-bottom: 1px solid #ddd;
                padding-bottom: 1rem;
            }
            .table tbody tr td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .table tbody tr td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 1rem;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <h2 class="text-center">CRUD Table</h2>

            <?php
            if (isset($_GET['msg'])) {
                $msg = htmlspecialchars($_GET['msg']);
                echo '
                <div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    ' . $msg . '
                </div>
                <script>
                    setTimeout(function() {
                        var alert = document.getElementById("alert");
                        if (alert) {
                            alert.classList.remove("show");
                        }
                    }, 2000);
                </script>
                ';
            }
            ?>

            <a href="register.php" class="btn btn-custom mb-3">Add New Record</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Family Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Mobile Number</th>
                        <th>Role</th>
                        <th>Confirm Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { 
                        // تعيين النص المناسب بناءً على قيمة role_id
                        $role_name = $row['role_id'] == 1 ? 'admin' : ($row['role_id'] == 2 ? 'user' : 'unknown');
                    ?>
                    <tr>
                        <td data-label="Image">
                            <?php
                            if ($row['image']) {
                                echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="User Image" />';
                            } else {
                                echo 'No Image';
                            }
                            ?>
                        </td>
                        <td data-label="ID"><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td data-label="First Name"><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td data-label="Middle Name"><?php echo htmlspecialchars($row['middle_name']); ?></td>
                        <td data-label="Last Name"><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td data-label="Family Name"><?php echo htmlspecialchars($row['family_name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td data-label="Password"><?php echo htmlspecialchars($row['password']); ?></td>
                        <td data-label="Mobile Number"><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                        <td data-label="Role"><?php echo htmlspecialchars($role_name); ?></td>
                        <td data-label="Confirm Password"><?php echo htmlspecialchars($row['confirm_password']); ?></td>
                        <td>
                            <a href="edit.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                            <a href="show.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>" class="btn btn-light"> <i class="bi bi-eye"></i> </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
