<?php
include "db_conn.php";

if(isset($_POST['submit'])) {
    // الحصول على القيم من النموذج
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $family_name = mysqli_real_escape_string($conn, $_POST['family_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $role_id = mysqli_real_escape_string($conn, $_POST['role_id']);
    $id = mysqli_real_escape_string($conn, $_GET['user_id']);

    // تحديث البيانات في قاعدة البيانات
    $sql = "UPDATE users SET first_name='$first_name', middle_name='$middle_name', last_name='$last_name', family_name='$family_name', email='$email', password='$password', confirm_password='$confirm_password', mobile_number='$mobile_number', role_id='$role_id' WHERE user_id='$id'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=User updated successfully");
        exit;
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}

// جلب بيانات المستخدم للتعديل
$id = mysqli_real_escape_string($conn, $_GET['user_id']);
$sql = "SELECT * FROM users WHERE user_id='$id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
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
        .main {
            width: 350px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        input {
            width: 100%;
            height: 40px;
            background: #e0dede;
            margin-bottom: 10px;
            padding: 10px;
            border: none;
            outline: none;
            border-radius: 5px;
        }
        button {
            width: 100%;
            height: 40px;
            background: #573b8a;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background: #6d44b8;
        }
    </style>
</head>
<body>
    <div class="main">
        <h2 class="text-center">Edit User Information</h2>
        <form action="" method="post">
            <input type="text" name="first_name" placeholder="First Name" required value="<?php echo htmlspecialchars($row['first_name']); ?>">
            <input type="text" name="middle_name" placeholder="Middle Name" value="<?php echo htmlspecialchars($row['middle_name']); ?>">
            <input type="text" name="last_name" placeholder="Last Name" required value="<?php echo htmlspecialchars($row['last_name']); ?>">
            <input type="text" name="family_name" placeholder="Family Name" value="<?php echo htmlspecialchars($row['family_name']); ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($row['email']); ?>">
            <input type="password" name="password" placeholder="Password" required value="<?php echo htmlspecialchars($row['password']); ?>">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required value="<?php echo htmlspecialchars($row['confirm_password']); ?>">
            <input type="text" name="mobile_number" placeholder="Mobile Number" required value="<?php echo htmlspecialchars($row['mobile_number']); ?>">
            <select name="role_id" required>
                <option value="1" <?php echo ($row['role_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                <option value="2" <?php echo ($row['role_id'] == 2) ? 'selected' : ''; ?>>User</option>
            </select>
            <button type="submit" name="submit">Update</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
