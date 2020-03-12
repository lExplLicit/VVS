<nav class="menu">
	<ul class="sidebar-menu metismenu" id="sidebar-menu">

		<li <?php if (strpos($_SERVER["PHP_SELF"], 'adminindex.php')) {
				echo 'class="active"';
			}; ?>>
			<a href="index.php">
				<i class="fa fa-home"></i> Dashboard </a>
		</li>


		<li <?php if (
				strpos($_SERVER["PHP_SELF"], 'dozenten.php') ||
				strpos($_SERVER["PHP_SELF"], 'adduser.php') ||
				strpos($_SERVER["PHP_SELF"], 'deluser.php') ||
				strpos($_SERVER["PHP_SELF"], 'blockuser.php')
			) {
				echo 'class="active"';
			}; ?>>
			<a href="dozenten.php">
				<i class="fa fa-group"></i> Dozenten </a>
		</li>

		<li <?php if (strpos($_SERVER["PHP_SELF"], 'admins.php') || strpos($_SERVER["PHP_SELF"], 'addadmin.php') || strpos($_SERVER["PHP_SELF"], 'deladmin.php') || strpos($_SERVER["PHP_SELF"], 'blockadmin.php')) {
				echo 'class="active"';
			}; ?>>
			<a href="admins.php">
				<i class="fa fa-group"></i> Administratoren </a>
		</li>

		<li <?php if (
				strpos($_SERVER["PHP_SELF"], 'kurse.php') ||
				strpos($_SERVER["PHP_SELF"], 'delkurs.php') ||
				strpos($_SERVER["PHP_SELF"], 'addkurs.php')
			) {
				echo 'class="active"';
			}; ?>>
			<a href="kurse.php">
				<i class="fa fa-pencil-square-o"></i> Kurse</a>
		</li>

		<li <?php if (
				strpos($_SERVER["PHP_SELF"], 'allevorlesungen.php') ||
				strpos($_SERVER["PHP_SELF"], 'addvorlesung.php') ||
				strpos($_SERVER["PHP_SELF"], 'viewvorlesung.php') ||
				strpos($_SERVER["PHP_SELF"], 'delvorlesung.php')
			) {
				echo 'class="active"';
			}; ?>>
			<a href="allevorlesungen.php">
				<i class="fa fa-puzzle-piece"></i> Vorlesungen</a>
		</li>




	</ul>
</nav>