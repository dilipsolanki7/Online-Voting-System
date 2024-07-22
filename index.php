

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP voting system</title>

    <!-- Bootstrap link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php
        session_start();

        // Database connection
        include('connect.php');

        // Define variables to hold error messages
        $nameErr = $mobileErr = $passErr = $emailErr = $stdErr = '';
        $username = $mobile = $email = $password = $std = '';


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $std = $_POST['std'];

            // Validating mobile number format using regular expression
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
                        $mobileErr = "Invalid phone number";
                    }
                }
                
            }
             else {
                // Query to check login credentials
                $sql = "SELECT * FROM `userdata` WHERE username='$username' 
                AND mobile='$mobile' AND email='$email' AND password='$password' AND standard='$std'";

                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {

                    // Fetching candidates if user is a voter
                    if ($std == 'Voter') {
                        $sql = "SELECT username,photo,votes,id FROM `userdata` WHERE standard='Candidate'";
                        $resultcandidate = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($resultcandidate) > 0) {
                            $candidates = mysqli_fetch_all($resultcandidate, MYSQLI_ASSOC);
                            $_SESSION['candidates'] = $candidates;
                        }
                    }

                    // Storing user session data
                    $data = mysqli_fetch_array($result);
                    $_SESSION['id'] = $data['id'];
                    $_SESSION['status'] = $data['status'];
                    $_SESSION['data'] = $data;

                    $username = $mobile = $email = $password= $std = '';

                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Invalid credentials
                    $passErr = "Invalid Credentials";
                }
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
    
    <h1 class="text-center">OVS</h1>
    <div class="container py-4 bg-light">
        <h2 class="text-center">Login</h2>
        <div class="container text-center"> 
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control w-50 m-auto <?php if($nameErr) echo 'is-invalid'; ?>" name="username" placeholder="Enter your username" required="required" value="<?php echo $username; ?>">
                    <span class="error"><?php echo $nameErr;?></span>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control w-50 m-auto  <?php if($mobileErr) echo 'is-invalid'; ?>" name="mobile" placeholder="Enter your mobile number" required="required" value="<?php echo $mobile; ?>">
                    <span class="invalid-feedback"><?php echo $mobileErr; ?></span>
                </div>
                <div class="mb-3">
                    <input type="text" name="email" class="form-control w-50 m-auto <?php if($emailErr) echo 'is-invalid'; ?>" id="email" placeholder="Enter your email" value="<?php echo $email; ?>">
                    <span class="error"><?php echo $emailErr;?></span>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control w-50 m-auto <?php if($passErr) echo 'is-invalid'; ?>" name="password" placeholder="Enter your password" required="required" value="<?php echo $password; ?>">
                    <span class="error"><?php echo $passErr;?></span>
                </div>
                <div class="mb-3">
                    <select name="std" class="form-select w-50 m-auto">
                        <option value="Candidate" <?php if ($std == 'Candidate') echo 'selected'; ?>>Candidate</option>
                        <option value="Voter" <?php if ($std == 'Voter') echo 'selected'; ?>>Voter</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark my-4">Login</button>
                <p>Don't have an account? <a href="registration.php" class="text-dark"> Register here</a></p>
            </form>
        </div>
    </div>
</body>
</html>

