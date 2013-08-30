<?
/************************************************************************************
 * MinaGrey Users Poll                                                              *
 * This is a simple, secure poll in PHP                                             *
 * Author: Nicla Rossini http://niclarossini.com                                    *
 * This poll was used on MinaGrey http://mina-grey.com                              *
 *                                                                                  *
 *  This program is free software: you can redistribute it and/or modify            *
 *  it under the terms of the GNU General Public License as published by            *
 *  the Free Software Foundation, either version 3 of the License, or               *
 *  (at your option) any later version.                                             *
 *                                                                                  *
 *  This program is distributed in the hope that it will be useful,                 *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of                 *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                   *
 *   GNU General Public License for more details.                                   *
 *                                                                                  *
 *  You should have received a copy of the GNU General Public License               *
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.           *
 *******                                                                       ******
 *                                                                                  *
 *                         (c) Nicla Rossini 2013                                   *
 ************************************************************************************           
 *  As long as you keep all notices intact, you can do what you want with this.     *
 *               If you modify it, add your name and a date                         *
 ************************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>MinaGrey Users Poll</title>

</head>

<body>
<center><h1>strong>MinaGrey Users Poll</strong></h1></center>

<? 
//find the actual IP of users

if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check if the ip comes from shared internet
    { //if statement
      $IP=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //check if the ip is passed from a proxy or wi-fi
    { //elseif statement (if instead. Use it carefully. Needs strict logic)
      $IP=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    { //else statement (otherwise...)
      $IP=$_SERVER['REMOTE_ADDR']; //if none of the abovementioned options, the IP is this one
    }
    
    
//now we sanitize the IP. FILTER_VALIDATE_IP must be used with an IF statement

if(filter_var($IP, FILTER_VALIDATE_IP)){ //if statement

//connect to database. We'll assume you're connected already

//check if the IP is already there...
$stm = "select id from votes where IP = '$IP'";
$stmn = $connection->query($stm) or die ("DB problem."); //"or die() must die", but this is just an example. You should use prepared statements.

//check if the query returns at least one row
if ($stmn->num_rows!=0) {
// if so...
echo "<center><font color=\"red\">You have voted already, thanks pal! Scroll down to see the results.</font></center>";
}else{ //otherwise...
if (isset($_POST["submit"])){ //check if someone posted somenthing


//sanitize to prevent injection
$comment = $_POST["comment"];
$NewComment= mysqli_real_escape_string($connection, $comment);
$v = $_POST["vote"];

//sanitize to prevent injection
$vote = filter_var($vote, FILTER_SANITIZE_STRING); 
$vote = mysqli_real_escape_string($connection, $vote); //you need to have the connection open.

//insert into database (connect to database first)
$add ="insert into someTable values (NULL,'$IP', '$vote', '$NewComment')";
$ad = $connection->query($add) or die ("Oops!".$connection->error.__LINE__); //"or die() should die", but we're using it 
$connection->close(); //close the connection
} //close if 
} //close else

}

?>

<center>

<div>
<!--this is a simple form -->
<form method = "post" action="<? $_SERVER['PHP_SELF']; //prevent XSS. You can also add a hidden input that generates a random signature, to be safer. ?>"> 
	<br/><strong>Do you think I can or should rebuild this website?<br/></strong>(the webmaster)<br/>
<br />
	Yes
<input type="radio" checked="checked" name="vote" value="yes" style="width: 33px; height: 25px"/>&nbsp;&nbsp;&nbsp; No 
	<input type="radio" name="vote" value="no" style="width: 28px; height: 24px"/>
<br/><br/>
Add a comment if you wish (optional):<br/>
<input type="text" name="comment" style="width: 301px; height: 33px"/><br/><br/>
<input type="submit" name="submit" value ="Push Me!"/>
</form>
<br/><br/>


</div> 

<div><!--how do we display poll results? Here's a very basic method-->
<center><strong>POOL RESULTS! <br/></strong><br/>
<div style="float:left">YES<br/>NO</div>
<table style="float:right;"><tr>
<? 
//connect to database first.

//let's use a prepared statement here
$stmnt = $connnection->prepare("select id from sometable where vote = ?");
$stmnt= bind_param("s", $result); //bind the parameters
$result = "yes"; //we want all votes that say yes
$stmnt->execute(); //execute the statement
if ($stmnt->num_rows !=0) { //if statement
while ($stmnt->fetch())
{ //while loop

echo "<td>&nbsp;</td>"; //print a td for each vote
}
}

$stmnt->close(); //close the statement

mysqli_close($connnection); //close the connection
?>

</tr><tr>
	
		<? 
	//do the same for the number of NOs here.
	//connect to database first.

//let's use a prepared statement here
$stmnt = $connection->prepare("select id from sometable where vote = ?");
$stmnt= bind_param("s", $result); //bind the parameters
$result = "no"; //we want all votes that say NO
$stmnt->execute(); //execute the statement
if ($stmnt->num_rows !=0) { //if statement
while ($stmnt->fetch())
{ //while loop

echo "<td>&nbsp;</td>"; //print a td for each vote
}
}

$stmnt->close(); //close the statement

mysqli_close($connection); //close the connection


?>
</tr>
</table></center></div>


</center>

</body>

</html>
