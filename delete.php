<?php
include "db_conn.php";

// تحقق من وجود المعلمة 'user_id'
if (isset($_GET['user_id'])) {
    // الحصول على القيمة من المعلمة
    $id = mysqli_real_escape_string($conn, $_GET['user_id']);

    // التأكد من أن القيمة عددية فقط
    if (is_numeric($id)) {
        // بناء الاستعلام بشكل صحيح
        $sql = "DELETE FROM `users` WHERE user_id = $id";

        // تنفيذ الاستعلام
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=Record deleted successfully");
            exit(); // تأكد من عدم تنفيذ أي كود بعد التوجيه
        } else {
            echo "Failed: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid ID.";
    }
} else {
    echo "No user ID specified.";
}

// إغلاق الاتصال بقاعدة البيانات
mysqli_close($conn);
?>
