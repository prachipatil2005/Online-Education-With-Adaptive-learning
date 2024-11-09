<?php

include '../components/connect.php';

if (isset($_POST['submit'])) {

   $id = unique_id();
   $name = $_POST['name'];// // Raw input from the form
   /*Sanitization is the process of cleaning user input to remove or neutralize any potentially harmful characters or code. 
   This prevents security issues such as SQL injection and Cross-Site Scripting (XSS). */
   $name = filter_var($name, FILTER_SANITIZE_STRING);// Remove or escape special characters
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_STRING);//filter flag
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id() . '.' . $ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/' . $rename;

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);

   if ($select_tutor->rowCount() > 0) {
      $message[] = 'Email already taken!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image) VALUES(?,?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $profession, $email, $cpass, $rename]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New tutor registered! please login now';
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body id="register1" style="padding-left: 0;">

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
      <div class="message form">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>

   <!-- register section starts  -->

   <section class="form-container">

      <form class="register" action="" method="post" enctype="multipart/form-data">
         <h3>Register new</h3>
         <div class="flex">
            <div class="col">
               <p>Your name <span>*</span></p>
               <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
               <p>Your profession <span>*</span></p>
               <select name="profession" class="box" required>
                  <option value="" disabled selected>-- Select your profession</option>
                  <option value="developer">Developer</option>
                  <option value="desginer">Desginer</option>
                  <option value="Musician">Musician</option>
                  <option value="biologist">Biologist</option>

                  <option value="teacher">Teacher
                  </option>
                  <option value="engineer">Engineer
                  </option>
                  <option value="lawyer">Lawyer
                  </option>
                  <option value="accountant">Accountant
                  </option>
                  <option value="doctor">Doctor
                  </option>
                  <option value="journalist">Journalist
                  </option>
                  <option value="photographer">Photographer</option>
               </select>
               <p>Your email <span>*</span></p>
               <input type="email" name="email" placeholder="enter your email" maxlength="20" required class="box">
            </div>
            <div class="col">
               <p>Your password <span>*</span></p>
               <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
               <p>Confirm password <span>*</span></p>
               <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">
               <p>Select pic <span>*</span></p>
               <input type="file" name="image" accept="image/*" required class="box">
            </div>
         </div>
         <p class="link">Already have an account? <a href="login.php">login now</a></p>
         <input type="submit" name="submit" value="register now" class="btn">
      </form>

   </section>

   <!-- registe section ends -->












   <script>
      let darkMode = localStorage.getItem('dark-mode');
      let body = document.body;

      const enabelDarkMode = () => {
         body.classList.add('dark');
         localStorage.setItem('dark-mode', 'enabled');
      }

      const disableDarkMode = () => {
         body.classList.remove('dark');
         localStorage.setItem('dark-mode', 'disabled');
      }

      if (darkMode === 'enabled') {
         enabelDarkMode();
      } else {
         disableDarkMode();
      }
   </script>

</body>

</html>