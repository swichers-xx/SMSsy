<?php
	//topnav.php - easy access to top navigation bar

$navout = "";
$baseurl = "https://www.luceresearch.com/sms";
$navout .= "<a href=\"".$baseurl."/dashboard.php\"><i class=\"fas fa-home\"></i>Dashboard</a>";

if($navusergroup == 1) {
	$navout .= "<a href=\"".$baseurl."/projects/\"><i class=\"fas fa-plus-circle\"></i>New Project</a>";
	$navout .= "<a href=\"".$baseurl."/reports/\"><i class=\"fas fa-file\"></i>Reports</a>";
	$navout .="<a href=\"".$baseurl."/conversations/\"><i class=\"fas fa-comments\"></i>Conversations</a>";
	$navout .="<a href=\"".$baseurl."/users/users.php\"><i class=\"fas fa-user-cog\"></i>Users</a>";
}
else {
	$navout .="<a href=\"".$baseurl."/conversations/\"><i class=\"fas fa-comments\"></i>Conversations</a>";
}
$navout .= "<a href=\"".$baseurl."/auth/logout.php\"><i class=\"fas fa-sign-out-alt\"></i>Logout</a>";
$logo = '<img src="'.$baseurl.'/layout/img/logo.svg">';
?>
<nav class="navtop">
		<div>
			<?php echo $logo;?>
			<h1>SMS</h1>
			<?php echo $navout;?>
		</div>
	</nav>
