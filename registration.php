<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting system - Registration Page</title>

    <!-- bootstrap link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="rstyle.css">
</head>
<body class="bg-dark">

    <?php
    include('connect.php');

    $nameErr = $mobileErr = $emailErr = $passErr = $cpassErr = $photoErr = $dobErr = '';
    $username = $mobile = $email = $password = $cpassword = $image = $std = $dob = '';


    if(isset($_POST['Registerme'])){
        $username = $_POST['username'];
        $mobile = $_POST['mobile'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $image = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];
        $std = $_POST['std'];

        

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($username)) {
                $nameErr = "Name is required";
            } else {
                $name = test_input($username);
                //Check if name only contains letters and whitespace
                if (!preg_match("/^[a-zA-Z ]*$/", $name)){
                    $nameErr = "Only letters and white space allowed";
                }
            }
    
            if (empty($_POST["email"])){
                $emailErr = "Email is required";
            } else {
                $email = test_input($_POST["email"]);
                //Check if e-mail address is well-formed
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $emailErr = "Invalid email format";
                }
            }
    
            if (empty($mobile)) {
                $mobileErr = "Phone number is required";
            } else {
                $mobile = test_input($mobile);
                //Check if phone number is well-formed
                if (!preg_match("/^[0-9]{10}$/", $mobile)){
                    $mobileErr = "Invalid Mobile number";
                }
            }

            if (empty($password)) {
                $passErr = "Password is required";
            } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/", $password)) {
                $passErr = "Password must contain at least one uppercase letter, one lowercase letter, one digit, one special character, and be at least 6 characters long";
            }
            
        
            if (empty($cpassword)) {
                $cpassErr = "Confirm password is required";
            } elseif ($password !== $cpassword) {
                $cpassErr = "Passwords do not match";
            }
        
            /* if (empty($image)) {
                $photoErr = "Photo is required";
            }  */
            if (empty($dob)) {
                $dobErr = "Date of birth is required";
            } else {
                $today = new DateTime();
                $birthdate = new DateTime($dob);
                $age = $birthdate->diff($today)->y; // Calculate age
                if ($std == 'Voter' && $age < 18) {
                    $dobErr = "Voters must be at least 18 years old";
                } elseif ($std == 'Candidate' && $age < 25) {
                    $dobErr = "Candidates must be at least 25 years old";
                }
            } 
            
        }
        
            move_uploaded_file($tmp_name, "C:/xampp/htdocs/OVS/images/" . $image);

            $sql = "INSERT INTO `userdata` (username, dob , mobile,email, password, photo, standard, status, votes) VALUES ('$username','$dob', '$mobile','$email', '$password', '$image', '$std', 0, 0)";
            $result = mysqli_query($conn, $sql);

            if($result){
                $username = $mobile = $email = $password = $cpassword = $image = $std = $dob = '';
                echo '<script>
                alert("Registration Successful");                
                
                window.location="index.php"; 
                </script>';  
                   
            } else {
                die(mysqli_error($conn));
            }
        
    }
    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>
    ?>     

    <h1>OVS</h1>
    <div class="py-4">
        
    <div class="container text-center">
    <h2 class="text-center">Register for Vote</h2>
        <form action="registration.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control w-50 m-auto <?php if($nameErr) echo 'is-invalid'; ?>" name="username" placeholder="Name as per EPIC card" required="required" value="<?php echo $username; ?>">
                <span class="error"><?php echo $nameErr;?></span>
            </div>
            <div class="mb-3">
                <input type="date" class="form-control w-50 m-auto" name="dob" placeholder="Enter your date of birth" value="<?php echo $dob; ?>">
                <span class="error"><?php echo $dobErr;?></span>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control w-50 m-auto <?php if($mobileErr) echo 'is-invalid'; ?>" name="mobile" placeholder="Enter your mobile number" required="required" value="<?php echo $mobile; ?>">
                <span class="invalid-feedback"><?php echo $mobileErr; ?></span>
            </div>
            <div class="mb-3">
                <input type="text" name="email" class="form-control w-50 m-auto" id="email" placeholder="Enter your email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $emailErr;?></span>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control w-50 m-auto" name="password" placeholder="Enter your password" required="required" value="<?php echo $password; ?>">
                <span class="error"><?php echo $passErr;?></span>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control w-50 m-auto" placeholder="Confirm Password" required="required" name="cpassword" value="<?php echo $cpassword; ?>">
                <span class="error"><?php echo $cpassErr;?></span>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control w-50 m-auto" name="photo" placeholder="Upload EPIC card">
                <span class="error"><?php echo $photoErr;?></span>
            </div>
            <div class="mb-3">
                <select name="std" class="form-control w-50 m-auto">
                    <option value="Candidate" <?php if ($std == 'Candidate') echo 'selected'; ?>>Candidate</option>
                    <option value="Voter" <?php if ($std == 'Voter') echo 'selected'; ?>>Voter</option>
                </select>
                <button type="submit" name="Registerme" class="btn btn-dark my-4">Register</button>
                <p>Already have an account? <a href="index.php" class="text-white">Login here</a></p>
            </div>
        </form>
    </div>

    </div>
    
</body>
</html>