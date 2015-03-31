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

if(!isset($_SESSION['fullname']))
{
  $_SESSION['fullname'] = "";
}


if(!isset($_SESSION['status']))
{
  $_SESSION['status'] = "";
}

if(!isset($_SESSION['info']))
{
  $_SESSION['info'] = "";
}

// Variable declaration
$emptyuser = "";
$emptypass = "";
$infomsg = "";

// Actions to be performed when Login button is clicked
if (isset($_POST["login"])=='Login')
{
    // This is to validate whether the Mandatory fields are filled are not
   if (empty($_POST["username"]) || empty($_POST["password"]))
   {
      if (empty($_POST["username"]))
      {
        $emptyuser = "* Username is required"; // If username is not enter, throw error message
      }
      if (empty($_POST["password"]))
      {
        $emptypass = "* Password is required";   // If Password is not enter, throw error message
      }
   }
   else
   {
      // Retrieving the valid login credentials and stored in a variable
      $username = strtolower($_POST["username"]);
      $password = md5($_POST["password"]);  // md5 methodology is used for password encryption
      // This is part where the php program will connect to the database
      try {
        $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";  // Connection String
        $dbh = new PDO("sqlite:$dbname");
        // Validating the login credentials by sending a database query
        $stmt = $dbh->prepare("SELECT * FROM users WHERE username='". $username . "' AND password='" . $password . "'");
        $execquery = $stmt->execute();
        //Returns 0 if the select query was not processed
        if($execquery == 0)
        {
            $_SESSION['info'] = "Unable to process the query, Please try again";
        }
        else
        {
          // Validate the Result set by using the Fetch method
          $query = array();
          $count = 0;
          while ($query = $stmt->fetch()) {
            $count = 1;  // Counter incremented if valid login
            // If the result set matched with the login credentials
            if(strtolower($query['username']) == $username && $query['password'] == $password)
            {
              // Assign values to the session variables
              $_SESSION['username'] = $username;
              $_SESSION['fullname'] = $query['fullname'];
              $_SESSION['status'] = "Active";
              session_write_close();
              // Call the next page that is Message Board page
              header("location: message.php");
              exit();
            }
          }
          if($count == 0) {  // Counter would remain 0 if it is invalid login
            $_SESSION['info'] = "Invalid Username/Password";
          }
        }
      } catch (PDOException $e) {   // Exception if the database connection is failed
          print "Error!: " . $e->getMessage() . "<br/>";
          die();
        }

   }
}

// Various informations are passed from different pages, all the messages are verified here and displayed according on the html tag
if(isset($_SESSION['info']) && $_SESSION['info'] != "")
{
  $infomsg = $_SESSION['info'];
  $_SESSION['info'] = "";
}

?>
<form action="board.php" method="POST">
<table width="309" border="1" align="center">
  <tr>
    <td colspan="2"><b><span>UTA Message Board</span></b></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Username</div></td>
    <td width="177"><input name="username" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyuser;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Password</div></td>
    <td><input name="password" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $emptypass;?></span></td>
  </tr>
  <tr>
    <td><div align="right"></div></td>
    <td align="center"><input name="login" type="submit" value="Login" /></td>
    <td nowrap><span style="color:red"><?php echo $infomsg;?></span></td>
  </tr>
</table>
</form>

<form action="register.php" method="POST">
<table width="309" border="1" align="center">
<tr><td>
<label>New User Registration:<label>
<input type="submit" name="register" value="New users must register here"/>
</td></tr>
</table>
</form>

</body>
</html>
