<?php
$startUser = 1000;
$password = 'test';
$title = 1;
$sem = array("Sem I", "Sem II", "Sem III", "Sem IV", "Sem V", "Sem VI", "Sem VII", "Sem VIII");
$branch = array("Automobile", "Biomedical", "Bio-Technology", "Chemical", "Civil", "Computer", "Electrical", "Electronics", "Electronics and Telecom", "Instrumentation", "Information Technology", "Mechanical", "Printing and Packaging Technology", "Production");
/*
	1001-1999 => Automobile
		1001-1008 => Sem I -> Sem VIII Student
		........
		1611-1618 => ^^^^^^^^^^^^^^^^^^^^^^^^^
		1621-1628 => Teacher
		1631-1638 => Office Staff
		1641-1648 => Admin
		1651-1658 => SuperAdmin
	2001-2999 => Biomedical
	3001 BioTechnology
	4001 Chemical
	5001 Civil
	6001 Computer
	7001 Electrical
	8001 Electronics
	9001 Electronics and Telecom
	10001 Instrumentation
	11001 Information Technology
	12001 Mechanical
	13001 Printing and Packaging Technology
	14001-14658 Production
*/
$branchIndex = 0;
$end = false;
$passwordT = password_hash($password, PASSWORD_BCRYPT);	
$predictValue = 0;
$sql = array();
$sqlIndex = 0;
while(true) {
	$header = "INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`, `semester`, `branch`, `ip`) VALUES ";
	$header2 = "-- Fake User Details".
		   "\n-- BRANCH_(ROLE)SEM_Number".
		   "\n-- Branches:".
		   "\n-- 	AUTO => Automobile".
		   "\n-- 	BIOM => Biomedical".
		   "\n-- 	BIOT => BioTechnology".
		   "\n-- 	CHEM => Chemical".
		   "\n-- 	CIVI => Civil".
		   "\n-- 	COMP => Computer".
		   "\n-- 	ELEC => Electrical".
		   "\n-- 	ETRX => Electronics".
		   "\n-- 	EXTC => Electronics and Telecom".
		   "\n-- 	INST => Instrumentation".
		   "\n-- 	INFO => Information Technology".
		   "\n-- 	MECH => Mechanical".
		   "\n-- 	PAPT => Printing and Packaging Technology".
		   "\n-- 	PROD => Production".
		   "\n-- Role:".
		   "\n-- 	s => Student".
		   "\n-- 	t => Teacher".
		   "\n-- 	o => Office Staff".
		   "\n-- 	a => Admin".
		   "\n-- 	sa => Super Admin".
		   "\n-- SEM:".
		   "\n-- 	Sem I-SemVIII => 1-8".
		   "\n-- Number:".
		   "\n-- 	Student => 1-62".
		   "\n-- 	Everyone Else => 1\n\n";
	$sql[$sqlIndex] = $header2 . $header;
	while(true) {
		echo "$branchIndex\n";
		if ($branchIndex == count($branch)) {
			$end = true;
			break;
		}
		$titleIndex = 0;
		$title = 1;
		$currentUser = $startUser*($branchIndex+1)+($titleIndex*10);
		$branchValue = $branch[$branchIndex];
		while (true) {
			echo "Title:". $title.PHP_EOL;
			if ($title > 16)
				break;
			$semIndex = 0;
			$semCount = 0;
			while (true) {
				//print "Sem:".$semIndex.PHP_EOL;
				if ($semIndex == count($sem)) {
					if ($semCount < 61 && $title == 1) {
						$titleIndex += 1;
						$currentUser = $startUser*($branchIndex+1)+($titleIndex*10);
						$semCount += 1;
						$semIndex = 0;
					} else
						break;
				}
				$currentUser++;
				/**
				 * Special Calculation for UserName
				 */
				$temp = floor($currentUser/1000);
				$branchT = array('AUTO', 'BIOM', 'BIOT', 'CHEM', 'CIVI', 'COMP', 'ELEC', 'ETRX', 'EXTC', 'INST', 'INFO', 'MECH', 'PAPT', 'PROD');
				$temp2 = $branchT[$temp-1];
				$userName = $temp2 . "_";

				$temp = $currentUser-($temp*1000);
				if ($temp <= 618) {
					$userName .= "s";
					$name = "Student $currentUser";
					$temp3 = $temp%10;
					$temp4 = (($temp-$temp3)/10)+1;
				} else if ($temp <= 628) {
					$userName .= "t";
					$name = "Teacher $currentUser";
					$temp4 = 1;
				} else if ($temp <= 638) {
					$userName .= "o";
					$name = "OfficeStaff $currentUser";
					$temp4 = 1;
				} else if ($temp <= 648) {
					$userName .= "a";
					$name = "Admin $currentUser";
					$temp4 = 1;
				} else if ($temp <= 658) {
					$userName .= "sa";
					$name = "SuperAdmin $currentUser";
					$temp4 = 1;
				}
				
				$userName .= floor($temp%10)."_";
				
				$userName .= $temp4;

				$semValue = $sem[$semIndex++];
				$sql[$sqlIndex] .= "('$userName', '". $passwordT ."', 'a@a.com', '$name', '1', '$title', '$semValue', '$branchValue', '::1')";
				if ($predictValue > 0 && ($predictValue%2000) == 0) {
					$sql[$sqlIndex] .= ";\n";
					$sqlIndex++;
					$sql[$sqlIndex] = $header2 . $header;
				} else {
					$sql[$sqlIndex] .= ",\n";
				}
				$predictValue++;
			}
			$titleIndex += 1;
			$title = $title << 1;
			$currentUser = $startUser*($branchIndex+1)+($titleIndex*10);
		}
		$branchIndex++;
		continue;
	}
	if ($end == true)
		break;
}
for ($i = 0; $i < count($sql); $i++) {
	print "Writing ". ($i+1) ."/".count($sql) ."\n";
	$sql[$i][strlen($sql[$i])-2] = ";";
	$file = fopen("../sql/update_20161011_1737_changeUsers_$i.sql", "w");
	fwrite($file, $sql[$i]);
	fclose($file);
}
?>