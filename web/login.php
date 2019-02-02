<?php
$message="";
if(count($_POST)>0) {
  $conn = mysql_connect("localhost","root","Seh0531");
  mysql_select_db("oc_orders",$conn);
  $result = mysql_query("SELECT * FROM users WHERE username='" . $_POST["username"] . "' and password = '". $_POST["password"]."'");
  $count  = mysql_num_rows($result);
  if($count==0) {
    $message = "Invalid Username or Password!";
  } else {
    $message = "You are successfully authenticated!";
  }
}
?>

<html>
<head>
  <title>User Login</title>
  <link rel="stylesheet" type="text/css" href="styles.css" />
</head>
<body>
  <form name="frmUser" method="post" action="index2.php">
    <div class="message"><?php if($message!="") { echo $message; } ?></div>
    <table border="0" cellpadding="10" cellspacing="1" width="500" align="center">
      <tr class="tableheader">
        <td align="center" colspan="2">Enter Login Details</td>
      </tr>
      <tr class="tablerow">
        <td align="right">Username</td>
        <td><input type="text" name="username"></td>
      </tr>
      <tr class="tablerow">
        <td align="right">Password</td>
        <td><input type="password" name="password"></td>
      </tr>
      <tr class="tableheader">
        <td align="center" colspan="2"><input type="submit" name="submit" value="Submit"></td>
      </tr>
    </table>
  </form>
</body></html>
