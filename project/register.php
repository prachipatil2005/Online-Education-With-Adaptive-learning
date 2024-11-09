<?php
include 'components/connect.php';

// Handle form submission
if (isset($_POST['submit'])) {
    $id = unique_id();

    // Sanitize and validate form data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } elseif ($pass !== $cpass) {
        $message[] = 'Passwords do not match!';
    } else {
        // Hash the password
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

        // Handle image upload
        $image = $_FILES['image'];
        $image_name = filter_var($image['name'], FILTER_SANITIZE_STRING);
        $image_size = $image['size'];
        $image_tmp_name = $image['tmp_name'];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = pathinfo($image_name, PATHINFO_EXTENSION);

        if (!in_array($ext, $allowed_extensions)) {
            $message[] = 'Invalid image format!';
        } elseif ($image_size > 5000000) { // 5MB limit
            $message[] = 'Image file size too large!';
        } else {
            $rename = unique_id() . '.' . $ext;
            $image_folder = 'uploaded_files/' . $rename;

            // Check if email already exists
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_user->execute([$email]);

            if ($select_user->rowCount() > 0) {
                $message[] = 'Email already taken!';
            } else {
                // Insert new user into the database
                $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
                $insert_user->execute([$id, $name, $email, $hashed_pass, $rename]);

                // Move the uploaded image file
                move_uploaded_file($image_tmp_name, $image_folder);

                // Redirect to login page after successful registration
                header('Location: login.php');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">
    <form class="register" action="" method="post" enctype="multipart/form-data">
        <h3>Create Account</h3>
        <div class="flex">
            <div class="col">
                <p>Your Name <span>*</span></p>
                <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
                <p>Your Email <span>*</span></p>
                <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
            </div>
            <div class="col">
                <p>Your Password <span>*</span></p>
                <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">
                <p>Confirm Password <span>*</span></p>
                <input type="password" name="cpass" placeholder="Confirm your password" maxlength="20" required class="box">
            </div>
        </div>
        <p>Select Profile Picture <span>*</span></p>
        <input type="file" name="image" accept="image/*" required class="box">
        <p class="link">Already have an account? <a href="login.php">Login now</a></p>
        <input type="submit" name="submit" value="Register Now" class="btn">
    </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
