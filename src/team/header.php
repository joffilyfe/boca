<?php
////////////////////////////////////////////////////////////////////////////////
//BOCA Online Contest Administrator
//    Copyright (C) 2003-2012 by BOCA Development Team (bocasystem@gmail.com)
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
////////////////////////////////////////////////////////////////////////////////
// Last modified 21/jul/2012 by cassio@ime.usp.br
ob_start();
header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-Type: text/html; charset=utf-8");
session_start();
ob_end_flush();
require_once('../version.php');

require_once("../globals.php");
require_once("../db.php");
$runteam='run.php';

echo "<html><head><title>Team's Page</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- Optional theme -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"> -->

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<?php
//echo "<meta http-equiv=\"refresh\" content=\"60\" />"; 

if(!ValidSession()) {
	InvalidSession("team/index.php");
	ForceLoad("../index.php");
}
if($_SESSION["usertable"]["usertype"] != "team") {
	IntrusionNotify("team/index.php");
	ForceLoad("../index.php");
}
?>


<div class="container">
<!-- 	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="/team/index.php">
					<span><img alt="BOCA" src="../images/smallballoontransp.png"> Boca</span>
				</a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-4">
				<p class="navbar-text navbar-right">
					<?php list($clockstr,$clocktype)=siteclock(); ?>
					<?php echo $clockstr; ?>
				</p>
			</div>
		</div>
	</nav> -->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-9">
				<ul class="nav navbar-nav">
					<li><a href="problem.php">Problems</a></li>
					<li><a href="run.php">Runs</a></li>
					<li><a href="score.php">Score</a></li>
					<li><a href="clar.php">Clarifications</a></li>
					<li><a href="task.php">Tasks</a></li>
					<li><a href="files.php">Backups</a></li>
					<li><a href="option.php">Options</a></li>
					<li><a href="../index.php">Logout</a></li>
				</ul>
				<p class="navbar-text navbar-right">
					<?php list($clockstr,$clocktype)=siteclock(); ?>
					<?php echo $clockstr; ?>
				</p>
				<p class="navbar-text navbar-right">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo $_SESSION["usertable"]["userfullname"]; ?>,
					Site=<?php echo $_SESSION["usertable"]["usersitenumber"]; ?>
				</p>
			</div>
		</div>
	</nav>
	<div class="alert alert-info" role="alert">
		Balloons: 
		<?php 
			$ds = DIRECTORY_SEPARATOR;
			if($ds=="") $ds = "/";

			$runtmp = $_SESSION["locr"] . $ds . "private" . $ds . "runtmp" . $ds . "run-contest" . $_SESSION["usertable"]["contestnumber"] . 
			"-site". $_SESSION["usertable"]["usersitenumber"] . "-user" . $_SESSION["usertable"]["usernumber"] . ".php";
			$doslow=true;
			if(file_exists($runtmp)) {
				if(($strtmp = file_get_contents($runtmp,FALSE,NULL,-1,1000000)) !== FALSE) {
					$postab=strpos($strtmp,"\t");
					$conf=globalconf();
					$strcolors = decryptData(substr($strtmp,$postab+1,strpos($strtmp,"\n")-$postab-1),$conf['key'],'');
					$doslow=false;
					$rn=explode("\t",$strcolors);
					$n=count($rn);
					for($i=1; $i<$n-1;$i++) {
						echo "<img alt=\"".$rn[$i]."\" width=\"10\" ".
						"src=\"" . balloonurl($rn[$i+1]) . "\" />\n";
						$i++;
					}
				} else unset($strtmp);
			}
			if($doslow) {
				$run = DBUserRunsYES($_SESSION["usertable"]["contestnumber"],
					$_SESSION["usertable"]["usersitenumber"],
					$_SESSION["usertable"]["usernumber"]);
				$n=count($run);
				for($i=0; $i<$n;$i++) {
					echo "<img alt=\"".$run[$i]["colorname"]."\" width=\"10\" ".
					"src=\"" . balloonurl($run[$i]["color"]) . "\" />\n";
				}
			}

			if(!isset($_SESSION["popuptime"]) || $_SESSION["popuptime"] < time()-120) {
				$_SESSION["popuptime"] = time();

				if(($st = DBSiteInfo($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usersitenumber"])) != null) {
					$clar = DBUserClars($_SESSION["usertable"]["contestnumber"],
						$_SESSION["usertable"]["usersitenumber"],
						$_SESSION["usertable"]["usernumber"]);
					for ($i=0; $i<count($clar); $i++) {
						if ($clar[$i]["anstime"]>$_SESSION["usertable"]["userlastlogin"]-$st["sitestartdate"] && 
							$clar[$i]["anstime"] < $st['siteduration'] &&
							trim($clar[$i]["answer"])!='' && !isset($_SESSION["popups"]['clar' . $i . '-' . $clar[$i]["anstime"]])) {
							$_SESSION["popups"]['clar' . $i . '-' . $clar[$i]["anstime"]] = "(Clar for problem ".$clar[$i]["problem"]." answered)\n";
					}
				}
				$run = DBUserRuns($_SESSION["usertable"]["contestnumber"],
					$_SESSION["usertable"]["usersitenumber"],
					$_SESSION["usertable"]["usernumber"]);
				for ($i=0; $i<count($run); $i++) {
					if ($run[$i]["anstime"]>$_SESSION["usertable"]["userlastlogin"]-$st["sitestartdate"] && 
						$run[$i]["anstime"] < $st['sitelastmileanswer'] &&
						$run[$i]["ansfake"]!="t" && !isset($_SESSION["popups"]['run' . $i . '-' . $run[$i]["anstime"]])) {
						$_SESSION["popups"]['run' . $i . '-' . $run[$i]["anstime"]] = "(Run ".$run[$i]["number"]." result: ".$run[$i]["answer"] . ')\n';
					}
				}
			}

				$str = '';
				if(isset($_SESSION["popups"])) {
					foreach($_SESSION["popups"] as $key => $value) {
						if($value != '') {
							$str .= $value;
							$_SESSION["popups"][$key] = '';
						}
					}
					if($str != '') {
						MSGError('YOU GOT NEWS:\n' . $str . '\n');
					}
				}
			}
		?>

	</div>
