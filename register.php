<!-- Samuel Benison Jeyaraj Victor  -->
<!-- 1000995539  -->
<!-- http://omega.uta.edu/~sbj5539/project5/board.php -->
<html>
<head>
  <title>Message Board</title>
</head>
<body bgcolor="#C0C0C0">
<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

//Start session
session_start();

// Initial declaration of all the session variables that are used
if(!isset($_SESSION['username']))
{
  $_SESSION['username'] = "";
}

if(!isset($_SESSION['info']))
{
  $_SESSION['info'] = "";
}

// If the program is called from previous page i.e., Login page
if (isset($_POST["register"])=='New users must register here')
{
  $_SESSION['username'] = "";
  $_SESSION['status'] == "";
  $_POST["username"] = "";
  $_POST["password"] = "";
  unset($_SESSION['username']);
  unset($_POST["username"]);
  unset($_POST["password"]);
}
// If the user is tried to access the third page directly, it will redirect to Page 1 (Login page)
else if (!isset($_POST["newuser"]) && !isset($_POST["back"])) {
  if(!isset($_SESSION['status']) || $_SESSION['status'] == "") {
    if($_SESSION['username'] == "")
    {
      // Redirecting to the Login page
      $_SESSION['info'] = "Redirected to the home page";
      session_write_close();
      header("location: board.php");
      exit();
    }
  }
}

// Declaring the variables
$emptyuser = "";
$emptypass = "";
$repass = "";
$emptyname = "";
$emptyemail = "";
$infomsg = "";

$dispusername = "";
$dispemail = "";
$dispfullname = "";

// Actions to be taken when the Register button is clicked
if (isset($_POST["newuser"]) == 'Register')
{
    // Using these variable to prefil the entered data in case the givin data has some error
    $dispusername = $_POST["username"];
    $dispfullname = $_POST["fullname"];
    $dispemail = $_POST["email"];

    // This is to validate whether the Mandatory fields are filled are not
    if (empty($_POST["username"]))
    {
      $emptyuser = "* Username is required";
    }
    else if (empty($_POST["password"]))
    {
      $emptypass = "* Password is required";
    }
    else if (empty($_POST["repassword"]))
    {
      $repass = "* Retype Password is required";
    }
    else if (empty($_POST["fullname"]))
    {
      $emptyname = "* Full Name is required";
    }
    else if (empty($_POST["email"]))
    {
      $emptyemail = "* Email is required";
    }
    else if ($_POST["password"] != $_POST["repassword"])
    {
      $repass = "* Password does not match";  // If the enter passsword does not match with Reentered Password
    }
    else
    {
      // Getting the values given as a input for Registration
      $username = strtolower($_POST["username"]);
      $password = md5($_POST["password"]);
      $fullname = $_POST["fullname"];
      $email = $_POST["email"];
      // This is part where the php program will connect to the database
      try {
        $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
        $dbh = new PDO("sqlite:$dbname");
        // Query to validate if the given username already exists or not
        $stmt = $dbh->prepare("SELECT * FROM users WHERE username='". $username . "'");
        $execquery = $stmt->execute();
        if($execquery == 0)
        {
            $_SESSION['info'] = "Unable to process the query, Please try again";
        }
        else
        {
          $query = array();
          $count = 0;
          while ($query = $stmt->fetch()) {
            $count = 1;
          }
          if($count == 1) {  // If the username already exists, throw an error
            $_SESSION['info'] = "Username already exists";
          }
          else
          {
            // Else Proceed with the insertion of record
            try {
              $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
              $dbh = new PDO("sqlite:$dbname");
              $dbh->beginTransaction();
              // Inserting the record to the table users
              $dbh->exec("INSERT into users values('" . $username . "','" . $password . "','" . $fullname . "','" . $email . "')")
                    or die(print_r($dbh->errorInfo(), true));
              $dbh->commit();
              $_SESSION['info'] = "New User created successfully";
              session_write_close();
              header("location: board.php");  // Redirect to Login page once the Registration is complete
              exit();
            }
            catch (PDOException $e) {
              print "Error!: " . $e->getMessage() . "<br/>";
              die();
            }
          }
        }
      } catch (PDOException $e) {
          print "Error!: " . $e->getMessage() . "<br/>";
          die();
        }

   }
}

// Redirect to Login page upon clicking back button
if (isset($_POST["back"])=='Goback')
{
    session_write_close();
    header("location: board.php");
    exit();
}

// Displaying the information message on to the HTML tag
if(isset($_SESSION['info']) && $_SESSION['info'] != "")
{
  $infomsg = $_SESSION['info'];
  $_SESSION['info'] = "";
}

?>
<form action="register.php" method="POST">
<table width="309" border="1" align="center">
  <tr>
    <td colspan="2"><b><span>New User Registration</span>
  </b>
  </td>
  </tr>
  <tr>
    <td width="116"><div align="right">Username</div></td>
    <td width="177"><input name="username" value="<?php echo $dispusername; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyuser;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Password</div></td>
    <td><input name="password" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $emptypass;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Retype Password</div></td>
    <td><input name="repassword" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $repass;?></span></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Fullname</div></td>
    <td width="177"><input name="fullname" value="<?php echo $dispfullname; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyname;?></span></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Email</div></td>
    <td width="177"><input name="email" value="<?php echo $dispemail; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyemail;?></span></td>
  </tr>
  <tr>
    <td align="center"><input name="back" type="submit" value="Goback" /></td>
    <td align="center"><input name="newuser" type="submit" value="Register" /></td>
    <td nowrap><span style="color:red"><?php echo $infomsg;?></span></td>
  </tr>
</table>
</form>
</body>
</html>