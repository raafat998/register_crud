
<?php
include "db_conn.php";

$error_message = '';

if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $family_name = $_POST['family_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile_number = isset($_POST['mobile_number']) ? $_POST['mobile_number'] : '';

    // تحقق من وجود الصورة
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // التحقق من نوع الملف
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExts)) {
            $uploadFileDir = 'uploads/';
            // تحقق من وجود المجلد وإذا لم يكن موجودًا، قم بإنشائه
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $fileName;

            // نقل الملف إلى المجلد
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image = $fileName;
            } else {
                $error_message = "Error moving the uploaded file. Check folder permissions.";
            }
        } else {
            $error_message = "Unsupported file type.";
        }
    } else {
        $error_message = "Error uploading image.";
    }

    // تحديد الحد الأدنى للطول
    $min_length = 8;

    // التحقق من تطابق كلمتي المرور
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < $min_length) {
        $error_message = "Password must be at least $min_length characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error_message = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error_message = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error_message = "Password must contain at least one number.";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $error_message = "Password must contain at least one special character.";
    }

    // التحقق من صحة البريد الإلكتروني
    $pattern = "/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($pattern, $email)) {
        $error_message = "Invalid email format. Ensure:<br>
            - Valid characters before '@'.<br>
            - A valid domain name.<br>
            - A valid top-level domain (e.g., .com, .net).";
    }

    if (empty($error_message)) {
        // تشفير كلمة المرور
        $hashed_password = md5($password);

        // تحضير الاستعلام
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, family_name, email, password, mobile_number, role_id, image, confirm_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $role_id = 2; 

        // Bind parameters
        $stmt->bind_param("sssssssiss", $first_name, $middle_name, $last_name, $family_name, $email, $hashed_password, $mobile_number, $role_id, $image, $hashed_password);

        if ($stmt->execute()) {
            header("Location: rigester.php?msg=new user created successfully");
            exit();
        } else {
            $error_message = "Failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

// التحقق من عملية تسجيل الدخول
if (isset($_POST["login"])) {
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    // تشفير كلمة المرور
    $hashed_password = md5($password);

    // التحقق من وجود البريد الإلكتروني وكلمة المرور في قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
}

// إغلاق الاتصال بالقاعدة فقط مرة واحدة بعد جميع العمليات
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Slide Navbar</title>
    <link rel="stylesheet" type="text/css" href="slide navbar style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Jost', sans-serif;
            background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e);
        }

        .alert-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            width: auto;
            max-width: 300px;
            z-index: 9999;
        }

        @media (max-width: 768px) {
            .alert-custom {
                width: 90%;
                top: 10px;
                right: 5%;
            }
        }

        button {
            height: 40px;
            margin-top: 20px;
        }

        .gender {
            display: flex;
            justify-content: space-around;
            align-items: center;
            color: #fff;
        }

        .gender label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .gender input[type="radio"] {
            margin-right: 5px;
            cursor: pointer;
        }

        .main {
            width: 350px;
            height: auto;
            background: red;
            overflow: hidden;
            background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/cover;
            border-radius: 10px;
            box-shadow: 5px 20px 50px #000;
        }

        #chk {
            display: none;
        }

        .signup, .login {
            position: relative;
            width: 100%;
            padding: 20px;
        }

        label {
            color: #fff;
            font-size: 2.3em;
            justify-content: center;
            display: flex;
            margin: 20px 0;
            font-weight: bold;
            cursor: pointer;
            transition: .5s ease-in-out;
        }

        input {
            width: 80%;
            height: 40px;
            background: #e0dede;
            justify-content: center;
            display: flex;
            margin: 10px auto;
            padding: 12px;
            border: none;
            outline: none;
            border-radius: 5px;
        }

        button {
            width: 80%;
            height: 40px;
            margin: 10px auto;
            justify-content: center;
            display: block;
            color: #fff;
            background: #573b8a;
            font-size: 1em;
            font-weight: bold;
            outline: none;
            border: none;
            border-radius: 5px;
            transition: .2s ease-in;
            cursor: pointer;
        }

        button:hover {
            background: #6d44b8;
        }

        .login {
            margin-top: 200px;
            height: 800px;
            background: #eee;
            border-radius: 60% / 10%;
            transform: translateY(-180px);
            transition: .8s ease-in-out;
        }

        .login label {
            color: #573b8a;
            transform: scale(.6);
        }

        #chk:checked ~ .login {
            transform: translateY(-500px);
        }

        #chk:checked ~ .login label {
            transform: scale(1);
        }

        #chk:checked ~ .signup label {
            transform: scale(.6);
        }
    </style>
</head>
<body>
    <div class="main">
        <!-- ------------------------------------------# [   alert msg     ] #------------------------------------------------------------------------------------------------- -->
        <?php if (!empty($error_message)): ?>
            <div id="alert" class="alert alert-danger alert-custom">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <!-- ------------------------------------------# [   end alert msg ] #------------------------------------------------------------------------------------------------- -->
        <div class="signup">
            <label for="chk" aria-hidden="true">Sign up</label>
            <form id="signup-form" method="post" enctype="multipart/form-data">
                <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                <input type="text" name="middle_name" id="middle_name" placeholder="Middle Name" required>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                <input type="text" name="family_name" id="family_name" placeholder="Family Name" required>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <input type="text" name="mobile_number" id="mobile_number" placeholder="Mobile Number" required>
                <input type="file" name="image" id="image" accept="image/*" required>
                <button type="submit" name="submit">Submit</button>
            </form>
        </div>
        <div class="login">
            <label for="chk" aria-hidden="true">Login</label>
            <form id="login-form" method="post">
                <input type="email" name="email" id="login-email" placeholder="Email" required>
                <input type="password" name="pswd" id="login-password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hide alert after 3 seconds
            if ($('#alert').length) {
                setTimeout(function() {
                    $('#alert').fadeOut();
                }, 3000); // 3 seconds
            }
        });

        // Validate signup form
        document.getElementById('signup-form').addEventListener('submit', function(event) {
            // Get form elements
            var firstName = document.getElementById('first_name').value;
            var middleName = document.getElementById('middle_name').value;
            var lastName = document.getElementById('last_name').value;
            var familyName = document.getElementById('family_name').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            var mobileNumber = document.getElementById('mobile_number').value;
            var image = document.getElementById('image').files[0];

            // Initialize error message
            var errorMessage = '';

            // Validate password
            var minLength = 8;
            if (password.length < minLength) {
                errorMessage = "Password must be at least " + minLength + " characters long.";
            } else if (!/[A-Z]/.test(password)) {
                errorMessage = "Password must contain at least one uppercase letter.";
            } else if (!/[a-z]/.test(password)) {
                errorMessage = "Password must contain at least one lowercase letter.";
            } else if (!/[0-9]/.test(password)) {
                errorMessage = "Password must contain at least one number.";
            } else if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
                errorMessage = "Password must contain at least one special character.";
            } else if (password !== confirmPassword) {
                errorMessage = "Passwords do not match.";
            }

            // Validate email
            var emailPattern = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                errorMessage = "Invalid email format.";
            }

            // Validate image
            if (image) {
                var fileExtension = image.name.split('.').pop().toLowerCase();
                var allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                if (!allowedExts.includes(fileExtension)) {
                    errorMessage = "Unsupported file type.";
                }
            } else {
                errorMessage = "Please upload an image.";
            }

            // Display error message and prevent form submission if needed
            if (errorMessage) {
                alert(errorMessage);
                event.preventDefault(); // Prevent form submission
            }
        });

        // Validate login form
        document.getElementById('login-form').addEventListener('submit', function(event) {
            // Get form elements
            var email = document.getElementById('login-email').value;
            var password = document.getElementById('login-password').value;

            // Initialize error message
            var errorMessage = '';

            // Validate email
            var emailPattern = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                errorMessage = "Invalid email format.";
            }

            // Display error message and prevent form submission if needed
            if (errorMessage) {
                alert(errorMessage);
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>
