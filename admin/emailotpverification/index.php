<?php
session_start();
include_once('config.php');

if(isset($_POST['submit'])){
    $name = $_POST['username'];
    $email = $_POST['email'];
    $cnumber = $_POST['contactnumber'];
    $loginpass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $otp = mt_rand(100000, 999999);

    $ret = "SELECT id FROM tblusers WHERE emailId=:uemail";
    $queryt = $dbh->prepare($ret);
    $queryt->bindParam(':uemail', $email, PDO::PARAM_STR);
    $queryt->execute();
    $results = $queryt->fetchAll(PDO::FETCH_OBJ);

    if($queryt->rowCount() == 0){
        $emailverifiy = 0;
        $sql = "INSERT INTO tblusers(userName, emailId, ContactNumber, userPassword, emailOtp, isEmailVerify) VALUES(:fname, :emaill, :cnumber, :hashedpass, :otp, :isactive)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':fname', $name, PDO::PARAM_STR);
        $query->bindParam(':emaill', $email, PDO::PARAM_STR);
        $query->bindParam(':cnumber', $cnumber, PDO::PARAM_STR);
        $query->bindParam(':hashedpass', $loginpass, PDO::PARAM_STR);
        $query->bindParam(':otp', $otp, PDO::PARAM_STR);
        $query->bindParam(':isactive', $emailverifiy, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        if($lastInsertId){
            $_SESSION['emailid'] = $email;
            //Code for Sending Email
            $subject = "OTP Verification";
            $headers= "From:karansoniss143@gmail.com"."\r\n";
             		  "MIME-Version: 1.0"."\r\n";
             		  "Content-type: text/html; charset=utf-8"."\r\n";
            $ms = "<html><body><div><div>Dear $name</div></br></br>";
            $ms.= "<div style='padding-top:8px;'>Thank you for registering with us. OTP for for Account Verification is $otp</div><div></div></body></html>";
            mail($email, $subject, $ms, $headers);
            echo "<script>window.location.href='verify-otp.php'</script>";
        }else {
            echo "<script>alert('Something went wrong.Please try again');</script>";
        }
    } else{
        echo "<script>alert('Email id already associated with another account.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700">
<title>User Registration with email otp verification in PHP</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style>
body {
	color: #999;
	background: #e2e2e2;
	font-family: 'Roboto', sans-serif;
}
.form-control {
	min-height: 41px;
	box-shadow: none;
	border-color: #e1e1e1;
}
.form-control:focus {
	border-color: #00cb82;
}	
.form-control, .btn {        
	border-radius: 3px;
}
.form-header {
	margin: -30px -30px 20px;
	padding: 30px 30px 10px;
	text-align: center;
	background: #00cb82;
	border-bottom: 1px solid #eee;
	color: #fff;
}
.form-header h2 {
	font-size: 34px;
	font-weight: bold;
	margin: 0 0 10px;
	font-family: 'Pacifico', sans-serif;
}
.form-header p {
	margin: 20px 0 15px;
	font-size: 17px;
	line-height: normal;
	font-family: 'Courgette', sans-serif;
}
.signup-form {
	width: 390px;
	margin: 0 auto;	
	padding: 30px 0;	
}
.signup-form form {
	color: #999;
	border-radius: 3px;
	margin-bottom: 15px;
	background: #f0f0f0;
	box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
	padding: 30px;
}
.signup-form .form-group {
	margin-bottom: 20px;
}		
.signup-form label {
	font-weight: normal;
	font-size: 14px;
}
.signup-form input[type="checkbox"] {
	position: relative;
	top: 1px;
}
.signup-form .btn {        
	font-size: 16px;
	font-weight: bold;
	background: #00cb82;
	border: none;
	min-width: 200px;
}
.signup-form .btn:hover, .signup-form .btn:focus {
	background: #00b073 !important;
	outline: none;
}
.signup-form a {
	color: #00cb82;		
}
.signup-form a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>
<div class="signup-form">
    <form  method="post">
		<div class="form-header">
			<h2>Sign Up</h2>
			<p>Fill out this form for registration</p>
		</div>
        <div class="form-group">
			<label>Full Name</label>
        	<input type="text" class="form-control" name="username" required="required">
        </div>
        <div class="form-group">
			<label>Email Address</label>
        	<input type="email" class="form-control" name="email" required="required">
        </div>
		<div class="form-group">
			<label>Contact Number</label>
            <input type="text" class="form-control" name="contactnumber" required="required">
        </div>
		<div class="form-group">
			<label> Password</label>
            <input type="password" class="form-control" name="password" required="required">
        </div>        
        <div class="form-group">
			<label class="form-check-label"><a href="resend-otp.php">Resend OTP</a></label>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-block btn-lg" name="submit">Sign Up</button>
		</div>	
    </form>
	<div class="text-center small">Already have an account? <a href="login.php">Login here</a></div>
</div>
</body>
</html>