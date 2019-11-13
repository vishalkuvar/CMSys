<?php
/**
 * Contact Staff Page
 */
// Get CSS for Table
echo '<link rel="stylesheet type="text/css" href="'. $rootDir .'/css/account/table.css">';
$temp = 1;
// Prepare Query.
$query = "SELECT `id`, `name`, `email`, `branch` FROM `login` WHERE `title`='2' AND `branch`='". $login->branch ."'";	// 2 = Teacher, 1 = Student
$login->DB->query($query);
// Check if There's some output
if ($login->DB->result->num_rows > 0) {
	// Print Table
	echo " 
			<table border=1 width=30% class='cmsys-table'>
						<tr>
							<th>Sr.No</th>
							<th>Picture</th>
							<th>Name</th>
							<th>Branch</th>
							<th>Email</th>
						</tr>
	";
	// Print all Teacher's Detail
	while(($res = $login->DB->result->fetch_assoc())) {
		echo "
						<tr>
							<td>".$temp."</td>";
		// Get Teacher Picture
?>
					<td><center><img src="<?php
						$fileHandler = new FileHandler(UPLOAD_PICTURE);
						if (($image = $fileHandler->get($res['id'])) != NULL) {
							$base64 = 'data:image/png;base64,' . base64_encode($image);
							echo $base64;
						} else {
							echo $rootDir."/bg/242x200.svg";
						}
						?>" style="max-width:141px; max-height: 120px;" /></center></td>
<?php
		echo "				<td>". $res['name'] ."</td>
							<td>". $res['branch'] ."</td>
							<td>". $res['email'] ."</td>
						<tr>
				";
				$temp++;	// Increase Sr. No
	}
	echo "</table>";
} else {
	echo "No Teacher Found";
}
?>