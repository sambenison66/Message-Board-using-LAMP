<!-- Samuel Benison Jeyaraj Victor  -->
<!-- 1000995539  -->
<!-- http://omega.uta.edu/~sbj5539/project5/board.php -->
<html>
<head>
  <title>Message Board</title>
</head>
<body bgcolor="#C0C0C0">
<h1 align="center"><b>UTA Message Board</b></h1>
<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

//Start session
session_start();

// If the user is tried to access the second page directly, it will redirect to Page 1 (Login page)
if(!isset($_SESSION['username']) || $_SESSION['username'] == "")
{
  $_SESSION['info'] = "Redirected to the home page";
  session_write_close();
  header("location: board.php");
  exit();
}

// Print a Welcome message with the user's full name
echo "Welcome Mr." . $_SESSION['fullname'] . ",\n\n";

?>
<br>
<br>
<span><b> Here is the list of messages: </b></span>
<br>
<br>
<table border="1">
  <th>Name</th>
  <th>Date and Time</th>
  <th>Message</th>

<?php

// Declaring the variables
$errormsg = "";
$infmsg = "";

// Actions to be taken when the Post button is clicked
if (isset($_POST["add"])=='Post')
{
  // Show an error message if the message field is blank
  if (empty($_POST["message"]))
  {
      $errormsg = "Please enter some message and then click Post";
  }
  else
  {
    $id = uniqid();  // Get an unique id for the posting record
    $postedby = $_SESSION['username'];  // Get the username as the Post By user
    $message = $_POST["message"];  // Get the message

    // Connect with the database
    try {
      $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
      $dbh = new PDO("sqlite:$dbname");
      $dbh->beginTransaction();
      // Insert a record with the new message post
      $dbh->exec("INSERT into posts values('" . $id . "','" . $postedby . "', datetime('now') ,'" . $message . "')")
            or die(print_r($dbh->errorInfo(), true));
      $dbh->commit();
      $infmsg = "New Post created successfully";
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
      }
  }
}

// Once the record is inserted, next the MEssage board will display all the Messages
if($_SESSION['username'] != "")
{
  try {
      $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
      $dbh = new PDO("sqlite:$dbname");
      
      // Select query to retrieve the list of messages
      $stmt = $dbh->prepare('SELECT * from posts ORDER BY datetime(datetime) ASC');
      $stmt->execute();

      // Listing the messages into a table based on the Result Set
      $query = array();
      while ($query = $stmt->fetch()) {
        foreach ($query as $key => $value) {
          if($key == "1") {  // 1 is postedby
            echo "<tr>";
            echo "<td>" . $value . "</td>";
          } else if($key == "2") {  // 2 is datetime
            echo "<td>" . $value . "</td>";
          } else if($key == "3") {  //  3 is message
            echo "<td>" . $value . "</td>";
            echo "</tr>";
          }
        }
      }

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
      }
}

// Clear the session and cache once the logout button is clicked
// and Navigate to Login page
if (isset($_POST["logout"])=='Logout')
{
  unset($_SESSION['username']);
  unset($_SESSION['fullname']);
  unset($_SESSION['status']);
  unset($_POST["username"]);
  unset($_POST["password"]);
  unset($_POST["message"]);
  $_SESSION['info'] = "Logged Out Successfully";
  session_write_close();
  header("location: board.php");
  exit();
}

?>
</table>
<br>
<br>
<br>
<form action="message.php" method="POST">
<fieldset><legend>Enter Your message here:</legend>
<input style="width:500px; height:100px;" type="text" name="message"/>
<input type="submit" name="add" value="Post"/>
<br>
<span style="color:red"><?php echo $errormsg;?></span>
<br>
<span style="color:green"><?php echo $infmsg;?></span>
</fieldset>
<br>
<br>
<br>
<input style="width:150px; height:100px; align:center;" type="submit" name="logout" value="Logout"/>
</form>
</body>
</html>