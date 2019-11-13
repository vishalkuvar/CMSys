<?php

trait SubjectHandler {
	public $subjectsAssigned;	// For title 2,8,16, Subjects Assigned to them.
	/**
	 * Prefixes:
	 * C: Course
	 * L: Lab
	 * E/X: Elective
	 * P: Project
	 * S: subject
	 */
	
	/** @var int(hex) ID of Courses Offered(Hexadecimal) */
	private $coursesOffered = 0x3FFF;
	/** @var array Total Years and Their Acronym */
	private $allYears = array(
		1 => array("First Year", "F.E.", "Sem I"),
		2 => array("First Year", "F.E.", "Sem II"),
		3 => array("Second Year", "S.E.", "Sem III"),
		4 => array("Second Year", "S.E.", "Sem IV"),
		5 => array("Third Year", "T.E.", "Sem V"),
		6 => array("Third Year", "T.E.", "Sem VI"),
		7 => array("Fourth Year", "B.E.", "Sem VII"),
		8 => array("Fourth Year", "B.E.", "Sem VIII"),
	);
	/** @var array Engineering Courses with Their Subject Code Prefixes */
	private $allBranches = array();
	private $TallBranches = array(
		0x0001 => array("Automobile", array("FE", "AE")),
		0x0002 => array("Biomedical", array("FE", "SEBM", "TEBM", "BEBM")),
		0x0004 => array("Bio-Technology", array("FE", "BT")),
		0x0008 => array("Chemical", array("FE", "CH")),
		0x0010 => array("Civil", array("FE", "CE-")),
		0x0020 => array("Computer", array("FE", "CS", "CP")),
		0x0040 => array("Electrical", array("FE", "EE")),
		0x0080 => array("Electronics", array("FE", "EX")),
		0x0100 => array("Electronics and Telecom", array("FE", "ET")),
		0x0200 => array("Instrumentation", array("FE", "IS")),
		0x0400 => array("Information Technology", array("FE", "SEIT", "TEIT", "BEIT")),
		0x0800 => array("Mechanical", array("FE", "ME")),
		0x1000 => array("Printing and Packaging Technology", array("FE", "PP")),
		0x2000 => array("Production", array("FE", "PE")),
	);

	/**
	 * Checks if Branch entered is valid
	 * @method isValidBranch
	 * @param  string        $branch Branch Name
	 * @return bool                  true, if valid branch is entered, else false.
	 */
	public function isValidBranch($branch) {
		foreach ($this->allBranches as $branchId => $key) {
			if (strcmp($key[0], $branch) == 0)
				return true;
		}
		return false;
	}

	/**
	 * Checks if Valid Semester is Entered.
	 * @method isValidSem
	 * @param  string      $sem Semester(SemI/SemII/SemIII/...)
	 * @return bool              true, if valid Sem is entered, else false.
	 */
	public function isValidSem($sem) {
		foreach ($this->allYears as $yearId => $key) {
			if ($sem == $key[2]) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Generates Eligible Branches(CoursesOffered can be set low.)
	 * @method generateBranches
	 */
	private function generateBranches() {
		foreach ($this->TallBranches as $key => $value) {
			if (($this->coursesOffered&$key) > 0) {
				$this->allBranches[$key] = $value;
			}
		}
	}

	/**
	 * Generates List of Subjects a user is authorized for.
	 * @method generateValidSubjects
	 * @param  integer                $title  TitleID
	 * @param  integer                $userId Unique User Id
	 */
	private function generateValidSubjects($title, $userId) {
		if ($title >= 2) {
			$sql = "SELECT `subject_code` FROM `teacher_subjects` WHERE `teacher_id`='". $userId ."'";
			$this->DB->query($sql);
			$this->subjects = array();
			$i = 0;
			if ($this->DB->result->num_rows > 0) {
				while (($res = $this->DB->result->fetch_assoc())) {
					$this->subjects[$i++] = $res['subject_code'];
				}
			}
		}
	}

	/** 
	 * Subjects with Branches, in nice format.
	 * @var array
	 * @format
	 * Format: 	{
	 * 				[BranchID] =>	{
	 * 									[Sem] =>	{{'Subject Code', 'Subject Name'}},
	 *         						}
	 *    		}
	 */
	private $subjectBranch;

	/**
	 * Converts the RAW Subject Information into above $subjectBranch Array
	 * @method subjectsToBranches
	 */
	private function subjectsToBranches() {
		// Loop through all branches.
		foreach ($this->allBranches as $branchId => $branchDetails) {
			$branchName = $branchDetails[0];
			$branchCodes = $branchDetails[1];
			// Loop through all subjects.
			for ($i = 0; $i < count($this->allSubjects); $i += 2) {	// Recurse only Codes.
				// Get Branch Prefix and Subject Code.
				$subjectCodes = explode(' ', $this->allSubjects[$i]);
				$inBranch = false;
				// Loop through Valid branch codes, and see if match is found.
				for ($j = 0; $j < count($branchCodes); $j++) {
					if (strpos($subjectCodes[0], $branchCodes[$j]) !== FALSE) {
						$inBranch = true;
						break;
					}
				}
				
				// Add to $subjectBranch if valid match is found.
				if ($inBranch) {
					$sem = floor(intval(substr($subjectCodes[1], 0, 3))/100);
					$this->subjectBranch[$branchId][$sem][] = array($this->allSubjects[$i], $this->allSubjects[$i+1]);
				}

			}
		}
	}

	/** @var array All Subjects */
	private $allSubjects = array(
		// First Year - Sem I
		'FEC 101', 'Applied Mathematics-I',	// 1
		'FEC 102', 'Applied Physics-I',
		'FEC 103', 'Applied Chemistry-I',
		'FEC 104', 'Engineering Mechanics',
		'FEC 105', 'Basic Electrical & Electronics Enginerring',
		'FEC 106', 'Environmental Studies',
		'FEL 101', 'Basic Workshop Practice-I',
		// First Year - Sem II
		'FEC 201', 'Applied Mathematics-II',
		'FEC 202', 'Applied Physics-II',
		'FEC 203', 'Applied Chemistry-II',	// 10
		'FEC 204', 'Engineering Drawing',
		'FEC 205', 'Structured Programming Approach',
		'FEC 206', 'Communication Skills',
		'FEL 201', 'Basic Workshop Practice-II', // 14
		/** Computer Engineering */
		// Second Year - Sem III
		'CSC 301', 'Applied Mathematics III',
		'CSC 302', 'Object Oriented Programming Methodology',
		'CSC 303', 'Data Structures',
		'CSC 304', 'Digital Logic Design and Analysis',
		'CSC 305', 'Discrete Structures',
		'CSC 306', 'Electronics Circuits and Communication',
		// Second Year - Sem IV
		'CSC 401', 'Applied Mathemiatics IV',
		'CSC 402', 'Analysis of Algorithm',
		'CSC 403', 'Computer Organization and Architecture',
		'CSC 404', 'Database Management System',
		'CSC 405', 'Theoretical Computer Science',
		'CSC 406', 'Computer Graphics',
		// Third Year - Sem V
		'CPC 501', 'MicroProcessor',
		'CPC 502', 'Operating System',
		'CPC 503', 'Structured and Object Oriented Analysis and Design',
		'CPC 504', 'Computer Networks',
		'CPL 501', 'Web Technologies Laboratory',
		'CPL 502', 'Business Communication and Ethics',
		// Third Year - SEM VI
		'CPC 601', 'System Programming and Compiler Construction',
		'CPC 602', 'Software Engineering',
		'CPC 603', 'Distributed Database',
		'CPC 604', 'Mobile Communication and Computing',
		'CPE 6011', 'Operation Research',
		'CPE 6012', 'Project Management',
		'CPE 6013', 'German',
		'CPE 6014', 'French',
		'CPL 601', 'Network Programming Laboratory',
		// Fourth Year - Sem VII
		'CPC 701', 'Digital Signal Processing',
		'CPC 702', 'Cryptography and Signal Security',
		'CPC 703', 'Artificial Intelligence',
		'CPE 7021', 'Advance Algorithm',
		'CPE 7022', 'Computer Simulation and Modeling',
		'CPE 7023', 'Image Processing',
		'CPE 7024', 'Software Architecture',
		'CPE 7025', 'Soft Computing',
		'CPE 7026', 'ERP and Supply Chain Management',
		'CPP 701', 'Project I',
		'CPL 701', 'Network Threats and Attacks Laboratory',
		// Fourth Year - Sem VIII
		'CPC 801', 'Data Warehousing and Mining',
		'CPC 802', 'Human Machine Interaction',
		'CPC 803', 'Parallel and Distributed Systems',
		'CPE 8031', 'Machine Language',
		'CPE 8032', 'Embedded Systems',
		'CPE 8033', 'Adhoc wireless networks',
		'CPE 8034', 'Digital Forensic',
		'CPE 8035', 'Big Data Analysis',
		'CPP 802', 'Project II',
		'CPL 801', 'Cloud Computing Laboratory',
		/*Information Technology*/
		// Second Year - Sem III
		'SEITC 301', 'Applied Mathematics-III',
		'SEITC 302', 'Data Structure & Algorithm Analysis',
		'SEITC 303', 'Object Oriented Programming Methodology',
		'SEITC 304', 'Analog & Digital Circuits',
		'SEITC 305', 'Database Management Systems',
		'SEITC 306', 'Principles of Analog & Digital Communication',
		'SEITL 302', 'Data Structure & Algorithm Analysis',
		'SEITL 303', 'Object Oriented Programming Methodology',
		'SEITL 304', 'Analog & Digital Circuits',
		'SEITL 305', 'Database Management Systems',
		'SEITL 306', 'Principles of Analog & Digital Communication',
		// Second Year - Sem IV
		'SEITC 401', 'Applied Mathematics-IV',
		'SEITC 402', 'Computer Networks',
		'SEITC 403', 'Computer Organization and Architecture',
		'SEITC 404', 'Automata Theory',
		'SEITC 405', 'Web Programming',
		'SEITC 406', 'Information Theory and Coding',
		'SEITL 402', 'Computer Networks',
		'SEITL 405', 'Web Programming',
		// Third Year - SEM V
		'TEITC 501', 'Computer Graphics and Virtual Reality', 
		'TEITC 502', 'Operating Systems',
		'TEITC 503', 'Microcontroller and Embedded Systems',
		'TEITC 504', 'Advanced Database Management Systems',
		'TEITC 505', 'Open Source Technologies',
		'TEITC 506', 'Business Communication and Ethics',
		'TEITL 501', 'Computer Graphics and Virtual Reality', 
		'TEITL 502', 'Operating Systems',
		'TEITL 503', 'Microcontroller and Embedded Systems',
		'TEITL 504', 'Advanced Database Management Systems',
		'TEITL 505', 'Open Source Technologies',
		// Third Year - SEM VI
		'TEITC 601', 'Software Engineering',
		'TEITC 602', 'Distributed Systems',
		'TEITC 603', 'System and Web Security',
		'TEITC 604', 'Data Mining and Business Intelligence',
		'TEITC 605', 'Advance Internet Technology',
		'TEITL 601', 'Software Engineering',
		'TEITL 602', 'Distributed Systems',
		'TEITL 603', 'System and Web Security',
		'TEITL 604', 'Data Mining and Business Intelligence',
		'TEITL 605', 'Advance InternetTechnology',
		// Fourth Year - Sem VII
		'BEITC 701',  'Software Project Management',
		'BEITC 702',  'Cloud Computing',
		'BEITC 703', 'Intelligent System',
		'BEITC 704', 'Wireless Technology',
		'BEITC 7051', 'Image Processing',
		'BEITC 7052', 'Software Architecture',
		'BEITC 7053', 'E-Commerce & E-Business',
		'BEITC 7054', 'Multimedia Systems',
		'BEITC 7055', 'Usability Engineering',
		'BEITC 7056', 'Ubiquitous Computing',
		'BEITL 701', 'Software Project Management',
		'BEITL 702', 'Cloud Computing',
		'BEITL 703', 'Intelligent System',
		'BEITL 704', 'Wireless Technology',
		'BEITT 705', 'Elective - I ',
		'BEITP 706', 'Project-I',
		// Fourth Year - Sem VIII
		'BEITC 801', 'Storage Network Management and Retrieval',
		'BEITC 802', 'Big Data Analytics',
		'BEITC 803', 'Computer Simulation and Modeling',
		'BEITC 8041', 'Enterprise Resource Planning',
		'BEITC 8042', 'Wireless Sensor Networks',
		'BEITC 8043', 'Geographical Information Systems',
		'BEITC 8044', 'Robotics',
		'BEITC 8045', 'Soft Computing',
		'BEITC 8046', 'Software Testing & Quality Assurance',
		'BEITL 801', 'Storage Network Management and Retrieval',
		'BEITL 802', 'Big Data Analytics',
		'BEITL 803', 'Computer Simulation and Modeling',
		'BEITL 804', 'Elective -II', 
		'BEITP 805', 'Project - II',
		// Automobile: AE
		// Second Year - Sem III
		'AEC 301', 'Applied Mathematics III',
		'AEC 302', 'Thermodynamics',
		'AEC 303', 'Strength of Materials',
		'AEC 304', 'Production Process- I',
		'AEL 305', 'Computer Aided M/c Drawing',
		'AEL 306', 'Data Base & Information Retrieval System',
		'AEL 307', 'Machine Shop Practice- I',
		// Second Year - Sem IV
		'AEC 401', 'Applied Mathematics IV',
		'AEC 402', 'Fluid Mechanics',
		'AEC 403', 'Theory of Machines- I',
		'AEC 404', 'Production Process- II',
		'AEC 405', 'Material Technology',
		'AEC 406', 'Industrial Electronics',
		'AEL 407', 'Machine Shop Practice- II',
		// Third Year - SEM V
		'AEC 501', 'I C Engines',
		'AEC 502', 'Metrology and Quality Engineering',
		'AEC 503', 'Production Process-III',
		'AEC 504', 'Theory of Machines- II',
		'AEC 505', 'Heat Transfer',
		'AEL 5O1', 'Business Communication and Ethics',
		// Third Year - SEM VI
		'AEC 601', 'Automotive System',
		'AEC 602', 'Machine Design I',
		'AEC 603', 'Mechanical Vibrations',
		'AEC 604', 'Thermal and Fluid Power Engineering',
		'AEC 605', 'Operations Research',
		'AEC 606', 'Finite Element Analysis',

		'AEC 701', 'Chassis Body Engineering',
		'AEC 702', 'CAD/CAM/CAE',
		'AEC 703', 'Automotive Design',
		'AEC 704', 'Product Design andDevelopment',
		'AEE 701X', 'Elective I',
		'AEP 701', 'Project I ',

		'AEC 801', 'Autotronics',
		'AEC 802', 'Vehicle Dynamics',
		'AEC 803', 'Vehicle Maintenance',
		'AEE 802X', 'Elective II',
		'AEP 802', 'Project II ',

		'AEE 7011', 'Power Plant Engineering & ',
		'AEE 7012', 'Supply Chain Management',
		'AEE 7013', 'Tribology',
		'AEE 7014', 'Computational Fluid Dynamics',
		'AEE 7015', 'Automotive Embedded Systems',
		'AEE 7016', 'Industrial Robotics',
		'AEE 7017', 'Transportation Management Motor Industry',

		'AEE 8021', 'Noise Vibrations & Harshness',
		'AEE 8022', 'Vehicle Safety',
		'AEE 8023', 'World Class Manufacturing ',
		'AEE 8024', 'Knowledge Management',
		'AEE 8025', 'Project Management ',
		'AEE 8026', 'Artificial Intelligence',
		'AEE 8027', 'Virtual Reality',

		// BioMedical : SEBM TEBM BEBM
		// Second Year - Sem III
		'SEBM 301', 'Applied Mathematics-III',
		'SEBM 302', 'Electronic Circuits and Design – I',
		'SEBM 303', 'Electrical Network Analysis and Synthesis',
		'SEBM 304', 'Human Anatomy and Physiology',
		'SEBM 305', 'Biomaterials ',
		'SEBM 306', 'Object Oriented Programming & Methodology',
		// Second Year - Sem IV
		'SEBM 401', 'Applied Mathematics-IV',
		'SEBM 402', 'Electronic Circuits and Design – II',
		'SEBM 403', 'Transducers and Sensors for Medical Applications',
		'SEBM 404', 'Logic Circuits ',
		'SEBM 405', 'Signals and Systems ',
		'SEBM 406', 'Electronic Instruments and Control System',
		// Third Year - SEM V
		'TEBM 501', 'Biomedical Instrumentation-I',
		'TEBM 502', 'Microprocessors ',
		'TEBM 503', 'Analog and Digital Circuits Design',
		'TEBM 504', 'Biomedical Digital Signal Processing',
		'TEBM 505', 'Principles of Communication Engineering',
		'TEBM 506', 'Business Communication and Ethics 	',
		// Third Year - SEM VI
		'TEBM 601', 'Biomedical Instrumentation –II ',
		'TEBM 602', 'Biostatistics ',
		'TEBM 603', 'Biological Modeling and Simulation',
		'TEBM 604', 'Microcontrollers and Embedded Systems',
		'TEBM 605', 'Medical Imaging –I ',
		'TEBM 606', 'Digital Image Processing',

		'BEBM 701', 'Biomedical Instrumentation-III ',
		'BEBM 702', 'Medical Imaging – II ',
		'BEBM 703', 'Biomechanics Prosthesis and Orthosis ',
		'BEBM 704', 'Very Large Scale Integrated Circuits ',
		'BEBM 705', 'Networking and Information System in Medicine ',
		'BEBM 706', 'Project Stage – I ',

		'BEBM 801', 'Nuclear Medicine ',
		'BEBM 802', 'Biomedical Microsystems ',
		'BEBM 803', 'Hospital Management ',
		'BEBM 804', 'Elective ',
		'BEBM 805', 'Project Stage – II ',
		'BEBM 8041', 'Lasers and Fiber Optics',
		'BEBM 8042', 'Robotics in Medicine',
		'BEBM 8043', 'Health care Informatics',
		'BEBM 8044', 'Rehabilitation Engineering',

		// BioTechnology: BT
		 // Second Year - Sem III
		'BTC 301', 'Applied MathematicsIII',
		'BTC 302', 'Microbiology ',
		'BTC 303', 'Cell Biology ',
		'BTC 304', 'Biochemistry ',
		'BTC 305', 'Unit Operations-I ',
		'BTC 306', 'Process Calculations ',
		'BTL 307', 'Microbiology Lab',
		'BTL 308', 'Biochemistry Lab',
		'BTL 309', 'Unit Operations-I Lab ',
		// Second Year - Sem IV
		'BTC 401', 'Applied Mathematics-IV ',
		'BTC 402', 'Molecular Genetics ',
		'BTC 403', 'Fermentation Technology',
		'BTC 404', 'Analytical Methods in Biotechnology',
		'BTC 405', 'Immunology and Immunotechnology',
		'BTC 406', 'Unit Operations-II ',
		'BTL 407', 'Fermentation Technology Lab ',
		'BTL 408', 'Analytical Methods in Biotechnology Lab ',
		'BTL 409', 'Unit Operations-II Lab',
		// Third Year - SEM V
		'BTC 501', 'Bioinformatics-',
		'BTC 502', 'Genetic Engineering ',
		'BTC 503', 'Biophysics ',
		'BTC 504', 'Thermodynamics & Biochemical Engineering',
		'BTC 505', 'Bioreactor Analysis & technology',
		'BTC 506', 'Business Communication & Ethics',
		'BTL 507', 'Lab – I',
		'BTL 508', 'Lab – II',
		// Third Year - SEM VI
		'BTC 601', 'Bioinformatics-II ',
		'BTC 602', 'Cell & Tissue Culture ',
		'BTC 603', 'Enzyme Engineering ',
		'BTC 604', 'IPR,Bioethics & Biosafety ',
		'BTC 605', 'Process Control & Instrumentation',
		'BTE 606', 'Elective – I ',
		'BTL 607', 'Lab – III',
		'BTL 608', 'Lab – IV',
		'BTL 609', 'Lab – V',
		// Fourth Year - SEM VII
		'BTC 701', 'Bioseparation & Downstream Processing Technology-I',
		'BTC 702', 'Bioprocess Modeling & Simulation',
		'BTS 703', 'Seminar',
		'BTE 704', 'Elective-II',
		'BTP 705', 'Project-A',
		'BTL 706', 'LAB VI',
		'BTL 707', 'LAB VII',

		'BTE 7041', 'Food Biotechnology',
		'BTE 7042', 'Pharmaceutical Technology',
		'BTE 7043', 'Nanotechnology',
		// Fourth Year - SEM VIII
		'BTC 801', 'Environmental Biotechnology',
		'BTC 802', 'Bioseparation & Downstream Processing Technology-II',
		'BTC 803', 'Bioprocess Plant & Equipment Design',
		'BTE 804', 'Elective-III',
		'BTP 805', 'Project-B',
		'BTL 806', 'LAB VIII',
		'BTL 807', 'LAB IX ',

		'BTE 8041', 'Non Conventional Sources of Energy',
		'BTE 8042', 'Biosensor & Diagnostics',
		'BTE 8043', 'Protein Engineering',
		'BTE 8044', 'Agriculture Biotechnology',

		// Chemical Engineering: CH
		// Second Year - Sem III
		'CHC 301', 'Applied Mathematics-III',
		'CHC 302', 'Engineering Chemistry-I',
		'CHC 303', 'Fluid Flow (FF) ',
		'CHC 304', 'Computer Programming & Numerical Methods',
		'CHC 305', 'Process Calculations ',
		'CHC 306', 'Chemical Engineering Economics',
		'CHL 307', 'Chem. Engg. Lab (FF)',
		'CHL 308', 'Engineering Chemistry Lab I',
		'CHL 309', 'Computer Programming & Numerical Methods Lab',
		// Second Year - Sem IV
		'CHC 401', 'Applied Mathematics-IV',
		'CHC 402', 'Engineering Chemistry-II',
		'CHC 403', 'Chemical Engg. Thermodynamics - I',
		'CHC 404', 'Material Science &Engineering',
		'CHC 405', 'Mechanical Equipment Design (MED)',
		'CHC 406', 'Solid Fluid Mechanical Operations (SFMO)',
		'CHL 407', 'Engineering Chemistry Lab II',
		'CHL 408', 'Chemical Engg Lab (SFMO)',
		'CHL 409', 'MED Lab',
		// Third Year - SEM V
		'CHC 501', 'Chemical Engineering Thermodynamics - II',
		'CHC 502', 'Mass Transfer Operations - I (MTO-I)',
		'CHC 503', 'Heat Transfer Operations – I (HTO-I)',
		'CHC 504', 'Chemical Reaction Engineering - I (CRE-I)',
		'CHC 505', 'Chemical Technology ',
		'CHC 506', 'Business Communication & Ethics',
		'CHL 507', 'Chemical Engg Lab (MTO-I) – ',
		'CHL 508', 'Chemical Engg Lab (CRE-I) – ',
		'CHL 509', 'Chemical Engg Lab (HTO-I) – ',
		'CHL 510', 'Chemical Engg Lab (Synthesis) ',
		// Third Year - SEM VI
		'CHC 601', 'Instrumentation ',
		'CHC 602', 'Mass Transfer Operations – II (MTO-II)',
		'CHC 603', 'Heat Transfer Operations – II (HTO-II)',
		'CHC 604', 'Chemical Reaction Engineering – II (CRE-II)',
		'CHC 605', 'Plant Engineering ',
		'CHE 606', 'Elective – I ',
		'CHL 607', 'Chemical Engg Lab (MTO-II) – ',
		'CHL 608', 'Chemical Engg Lab (CRE-II) – ',
		'CHL 609', 'Chemical Engg Lab (HTO-II) – ',

		'CHE 6061', 'Operation Research',
		'CHE 6062', 'Advanced Material',
		'CHE 6063', 'Computational',
		'CHE 6064', 'Fluid Dynamics',
		// Fourth Year - SEM VII
		'CHC 701', 'Process Equipment Design (PED)',
		'CHC 702', 'Process Engineering',
		'CHC 703', 'Process Dynamics & Control (PDC)',
		'CHE 704', 'Elective – II',
		'CHP 705', 'Project – A',
		'CHS 706', 'Seminar',
		'CHL 707', 'Chemical Engg Lab (PED)',
		'CHL 708', 'Chemical Engg Lab (PDC)',

		'CHE 7041', 'High Performance Leadership',
		'CHE 7042', 'Polymer Technology',
		'CHE 7043', 'Petroleum Refining Technology',
		'CHE 7044', 'Advanced Process Simulation',
		// Fourth Year - SEM VIII
		'CHC 801', 'Modelling, Simulation & Optimization (MSO)',
		'CHC 802', 'Project Engineering & Entrepreneurship Management',
		'CHC 803', 'Environmental Engineering (EE)',
		'CHC 804', 'Energy System Design',
		'CHE 805', 'Elective – III',
		'CHP 806', 'Project – B',
		'CHL 807', 'Chemical Engineering Lab (EE)',
		'CHL 808', 'Chemical Engg Lab (MSO)',


		// Civil Engineering CE-
		 // Second Year - Sem III
		'CE-C 301', 'Applied Mathematics-III',
		'CE-C 302', 'Surveying – I ',
		'CE-C 303', 'Strength of Materials ',
		'CE-C 304', 'Building Materials and Construction ',
		'CE-C 305', 'Engineering Geology ',
		'CE-C 306', 'Fluid Mechanics – I ',
		'CE-C 307', 'Database and Information Retrieval System',
		// Second Year - Sem IV
		'CE-C 401', 'Applied Mathematics – IV ',
		'CE-C 402', 'Surveying – II ',
		'CE-C 403', 'Structural Analysis – I ',
		'CE-C 404', 'Building Design and Drawing – I',
		'CE-C 405', 'Concrete Technology ',
		'CE-C 406', 'Fluid Mechanics – II ',
		// Third Year - SEM V
		'CE-C 501', 'Structural Analysis – II ',
		'CE-C 502', 'Geotechnical Engg.– I ',
		'CE-C 503', 'Building Design and Drawing – II',
		'CE-C 504', 'Applied Hydraulics – I ',
		'CE-C 505', 'Transportation Engg. – I ',
		'CE-C 506', 'Employment and Corporate Skills ',
		// Third Year - SEM VI
		'CE-C 601', 'Geotechnical Engg. – II ',
		'CE-C 602', 'Design and Drawing of Steel Structures ',
		'CE-C 603', 'Applied Hydraulics – II ',
		'CE-C 604', 'Transportation Engg. – II ',
		'CE-C 605', 'Environmental Engg – I ',
		'CE-C 606', 'Theory of Reinforced Prestressed Concrete ',
		// Fourth Year - Sem VII
		'CE-C 701', 'Limit State Method for Reinforced Concrete Structures',
		'CE-C 702', 'Quantity Survey Estimation and Valuation',
		'CE-C 703', 'Irrigation Engineering',
		'CE-C 704', 'Environmental Engineering – II',
		'CE-E 705', 'Elective – I',
		'CE-P 706', 'Project – Part I',

		// Fourth Year - Sem VIII
		'CE-C 801', 'Design and Drawing of Reinforced Concrete Structures',
		'CE-C 802', 'Construction Engineering',
		'CE-C 803', 'Construction Management',
		'CE-E 804', 'Elective – II',
		'CE-P 805', 'Project – Part II',

		// Electrical Engineering: EE
		// Second Year - Sem III
		'EEC 301', 'Applied Mathematics – III',
		'EEC 302', 'Electronic Devices and Circuits ',
		'EEC 303', 'Conventional and Nonconventional Power Generation',
		'EEC 304', 'Electrical Networks ',
		'EEC 305', 'Electrical and Electronic Measurements ',
		'EEC 306', 'Object Oriented Programming and Methodology',
		// Second Year - Sem IV
		'EEC 401', 'Applied Mathematics – IV',
		'EEC 402', 'Elements of Power System ',
		'EEC 403', 'Electrical Machines –I ',
		'EEC 404', 'Signal Processing ',
		'EEC 405', 'Analog and Digital Integrated Circuits ',
		'EEC 406', 'Numerical Methods and Optimization Techniques',
		// Third Year - SEM V
		'EEC 501', 'Protection and Switchgear Engineering ',
		'EEC 502', 'Electrical Machines - II ',
		'EEC 503', 'Electromagnetic Fields and Waves ',
		'EEC 504', 'Power Electronics ',
		'EEC 505', 'Communication Engineering ',
		'EEC 506', 'Business Communication and Ethics',
		// Third Year - SEM VI
		'EEC 601', 'Power System Analysis ',
		'EEC 602', 'Electrical Machines – III ',
		'EEC 603', 'Utilization of Electrical Energy ',
		'EEC 604', 'Control System – I ',
		'EEC 605', 'Microcontroller and its Applications',
		'EEC 606', 'Project Management',
		'EEC 701', 'Power System Operation and Control ',
		'EEC 702', 'High Voltage DC Transmission ',
		'EEC 703', 'Electrical Machine Design ',
		'EEC 704', 'Control System – II ',
		'EEE 70X', 'Elective I ',
		'EEC 706', 'Project- I ',
		'EEC 801', 'Design, Management and Auditing of Electrical Systems',
		'EEC 802', 'Drives and Control ',
		'EEC 803', 'Power System Planning and Reliability ',
		'EEE 80X', 'Elective- II ',
		'EEC 805', 'Project- II',

		'EEE 701', 'High Voltage Engineering',
		'EEE 801', 'Flexible AC Transmission Systems',
		'EEE 702', 'Analysis and Design of Power Switching Converters',
		'EEE 802', 'Electric and Hybrid Electric Vehicle Technology',
		'EEE 703', 'Power System Modelling EEE',
		'EEE 704', 'Digital Signal Controllers and its Application',
		'EEE 804', 'Smart Grid Technology',
		'EEE 705', 'Advanced Lighting Systems EEE',
		'EEE 706', 'Renewable Energy and Energy Storage Systems',
		'EEE 806', 'Non-linear Control System',
		'EEE 707', 'Optimization Techniques and its Applications',
		'EEE 807', 'Entrepreneurship Development',

		// Electronics Engineering: EX
		// Second Year - Sem III
		'EXS 301', 'Applied Mathematics III',
		'EXC 302', 'Electronic Devices ',
		'EXC 303', 'Digital Circuits and Design',
		'EXC 304', 'Circuit Theory ',
		'EXC 305', 'Electronic Instruments and Measurements',
		'EXL 301', 'Electronic Devices Laboratory',
		'EXL 302', 'Digital Circuits and Design Laboratory',
		'EXL 303', 'Circuit Theory and Measurements Laboratory',
		'EXL 304', 'Object Oriented Programming Methodology Laboratory',
		// Second Year - Sem IV
		'EXS 401', 'Applied Mathematics IV',
		'EXC 402', 'Discrete Electronic Circuits ',
		'EXC 403', 'Microprocessor and Peripherals',
		'EXC 404', 'Principles of Control Systems',
		'EXC 405', 'Fundamentals of Communication Engineering',
		'EXC 406', 'Electrical Machines ',
		'EXL 401', 'Discrete Electronics Laboratory',
		'EXL 402', 'Microprocessor and Peripherals Laboratory',
		'EXL 403', 'Control System and Electrical Machines Laboratory',
		'EXL 404', 'Communication Engineering Laboratory',
		// Third Year - SEM V
		'EXC 501', 'Microcontrollers and Applications',
		'EXC 502', 'Design with Linear Integrated Circuits',
		'EXC 503', 'Electromagnetic Engineering',
		'EXC 504', 'Signals and Systems ',
		'EXC 505', 'Digital Communication ',
		'EXS 506', 'Business Communication and Ethics',
		'EXL 501', 'Microcontrollers and Applications Laboratory',
		'EXL 502', 'Design with Linear Integrated Circuits Laboratory',
		'EXL 503', 'Digital Communication Laboratory',
		'EXL 504', 'Mini Project I',
		// Third Year - SEM VI
		'EXC 601', 'Basic VLSI Design ',
		'EXC 602', 'Advanced Instrumentation Systems',
		'EXC 603', 'Computer Organization ',
		'EXC 604', 'Power Electronics I ',
		'EXC 605', 'Digital Signal Processing and Processors',
		'EXC 606', 'Modern Information Technology for Management',
		'EXL 601', 'VLSI Design Laboratory -- ',
		'EXL 602', 'Advance Instrumentation and Power Electronics Laboratory',
		'EXL 605', 'Digital Signal Processing and Processors Laboratory',
		'EXL 603', 'Mini Project II ',
		'EXC 701', 'Embedded System Design',
		'EXC 702', 'IC Technology ',
		'EXC 703', 'Power Electronics –II ',
		'EXC 704', 'Computer Communication Networks',
		'EXC 705X', 'Elective - I',
		'EXC 706', 'Project - I',
		'EXL 701', 'Embedded System Design Laboratory',
		'EXL 702', 'IC Technology Laboratory',
		'EXL 703', 'Power Electronics –II Laboratory',
		'EXL 704', 'Computer Communication Networks Laboratory',
		'EXL 705X', 'Elective – I Laboratory ',

		'EXC 7051', 'Digital Image Processing',
		'EXC 7052', 'Artificial Intelligence',
		'EXC 7053', 'ASIC Verification',
		'EXC 7054', 'Optical Fiber Communication',

		'EXC 801', 'CMOS VLSI Design ',
		'EXC 802', 'Advanced Networking Technologies',
		'EXC 803', 'MEMS Technology ',
		'EXC 804X', 'Elective -II ',
		'EXC 806', 'Project - II ',
		'EXL 801', 'CMOS VLSI Design Laboratory',
		'EXL 802', 'Advanced Networking Technologies Laboratory',
		'EXL 803', 'MEMS Laboratory',
		'EXL 804X', 'Elective –II Laboratory',
		'EXC 8041', 'Robotics',
		'EXC 8042', 'Mobile Communication',
		'EXC 8043', 'Digital Control System',
		'EXC 8044', 'Biomedical Electronics',

		// Electronics and Telecom: ET
		// Second Year - Sem III
		'ETS 301', 'Applied Mathematics III ',
		'ETC 302', 'Analog Electronics I ',
		'ETC 303', 'Digital Electronics ',
		'ETC 304', 'Circuits and Transmission Lines',
		'ETC 305', 'Electronic Instruments and Measurements',
		'ETC 306', 'Object Oriented Programming Methodology',
		'ETL 301', 'Analog Electronics I Laboratory',
		'ETL 302', 'Digital Electronics Laboratory',
		'ETL 303', 'Circuits and Measurement Laboratory',
		'ETL 304', 'Object Oriented Programming Methodology, Laboratory ',
		// Second Year - Sem IV
		'ETS 401', 'Applied Mathematics IV',
		'ETC 402', 'Analog Electronics II ',
		'ETC 403', 'Microprocessor and Peripherals',
		'ETC 404', 'Wave Theory and Propagation',
		'ETC 405', 'Signals and Systems',
		'ETC 406', 'Control Systems ',
		'ETL 401', 'Analog Electronics II Laboratory',
		'ETL 402', 'Microprocessor and Peripherals Laboratory',
		'ETL 403', 'SSW Laboratory',
		// Third Year - SEM V
		'ETC 501', 'Microcontrollers and Applications',
		'ETC 502', 'Analog Communication ',
		'ETC 503', 'Random Signal Analysis',
		'ETC 504', 'RF Modeling and Antennas',
		'ETC 505', 'Integrated Circuits ',
		'ETS 506', 'Business Communication and Ethics',
		'ETL 501', 'Microcontrollers and Applications Laboratory',
		'ETL 502', 'Communication Engineering Laboratory I',
		'ETL 503', 'Communication Engineering Laboratory II',
		'ETL 504', 'Mini Project I',
		// Third Year - SEM VI
		'ETC 601', 'Digital Communication ',
		'ETC 602', 'Discrete Time Signal Processing',
		'ETC 603', 'Computer Communication and Telecom Networks',
		'ETC 604', 'Television Engineering ',
		'ETC 605', 'Operating Systems ',
		'ETC 606', 'VLSI Design ',
		'ETL 601', 'Discrete Time Signal Processing Laboratory',
		'ETL 602', 'Communication Engineering Laboratory III',
		'ETL 603', 'Communication Engineering Laboratory IV',
		'ETL 604', 'Mini Project II ',
		'ETC 701', 'Image and Video Processing',
		'ETC 702', 'Mobile Communication',
		'ETC 703', 'Optical Communication and Networks',
		'ETC 704', 'Microwave and Radar Engineering',
		'ETE 70X', 'Elective ',
		'ETL 701', 'Image and Video Processing Laboratory',
		'ETL 702', 'Advanced communication Engineering. Laboratory I',
		'ETL 703', 'Advanced communication Engineering. Laboratory II',
		'ETEL 70X', 'Elective',
		'ETP 701', 'Project (Stage I)',

		'ETE 701', 'Data Compression and Encryption',
		'ETE 702', 'Statistical Signal Processing',
		'ETE 703', 'Neural Network and Fuzzy Logic',
		'ETE 704', 'Analog and Mixed Signal VLSI',

		'ETC 801', 'Wireless Networks ',
		'ETC 802', 'Satellite communication and Networks',
		'ETC 803', 'Internet and Voice Communication',
		'ETE 80X', 'Elective ',
		'ETL 801', 'Wireless Networks Laboratory',
		'ETL 802', 'Satellite communication and Networks Laboratory',
		'ETL 803', 'Internet and Voice Communication Laboratory',
		'ETEL 80X', 'Elective Laboratory',
		'ETP 801', 'Project (Stage II) ',
		'ETE 801', 'Speech Processing',
		'ETE 802', 'Telecom Network Management',
		'ETE 803', 'Microwave Integrated Circuits',
		'ETE 804', 'Ultra Wideband Communication ',

		// Instrumentation: IS
		// Second Year - Sem III
		'ISC 301', 'Applied MathematicsIII',
		'ISC 302', 'Electrical Network Analysis and Synthesis',
		'ISC 303', 'Analog Electronics',
		'ISC 304', 'Digital Electronics',
		'ISC 305', 'Transducers-I',
		'ISC 306', 'Object oriented programming and methodology',
		// Second Year - Sem IV
		'ISC 401', 'Applied MathematicsIV',
		'ISC 402', 'Feedback Control System',
		'ISC 403', 'Electrical Technology and Instruments',
		'ISC 404', 'Communication System',
		'ISC 405', 'Transducers-II',
		'ISC 406', 'Application Software Practices',
		// Third Year - SEM V
		'ISC 501', 'Signals and Systems',
		'ISC 502', 'Applications of Microcontroller -I',
		'ISC 503', 'Control System Design',
		'ISC 504', 'Signal Conditioning Circuit Design',
		'ISC 505', 'Control system components',
		'ISC 506', 'Business Communication and Ethics',
		// Third Year - SEM VI
		'ISC 601', 'Process Instrumentation Systems',
		'ISC 602', 'Power Electronics and Drives',
		'ISC 603', 'Digital Signal Processing',
		'ISC 604', 'Applications of Microcontroller -II',
		'ISC 605', 'Industrial Data Communication',
		'ISC 606', 'Analytical Instrumentation',
		
		'ISC 701', 'Industrial Process Control',
		'ISC 702', 'Biomedical Instrumentation',
		'ISC 703', 'Advanced Control Systems',
		'ISC 704', 'Process Automation',
		'ISE 705X', 'Elective-I',
		'ISP 706', 'Project-I ',
		'ISC 801', 'Digital Control System',
		'ISC 802', 'Instrumentation Project Documentation and Execution',
		'ISC 803', 'Instrument and System Design',
		'ISE 804X', 'Elective II',
		'ISP 805', 'Project-II',

		'ISE 7053', 'Functional Safety',
		'ISE 8043', 'Optimal Control theory',
		'ISE 7054', 'Process Modeling & Optimization',
		'ISE 8044', 'Nano Technology',
		'ISE 7055', 'Wireless communication',
		'ISE 8045', 'Fiber Optic Instrumentation',

		// Printing and Packaging Technology: PP
		// Second Year - Sem III
		'PPC 301', 'Applied Mathematics - III',
		'PPC 302', 'Principles of Packaging Technology',
		'PPC 303', 'Introduction to Printing Technology',
		'PPC 304', 'Paper based Packaging Materials',
		'PPC 305', 'Principles of Graphic Arts & Design',
		'PPC 306', 'Material Science & Technology',
		'PPL 301', 'Screen Printing Laboratory ',
		// Second Year - Sem IV
		'PPC 401', 'Plastics in Packaging',
		'PPC 402', 'Glass, Metal & Textile based Packaging Materials',
		'PPC 403', 'Digital Imaging & Colour Management',
		'PPC 404', 'Offset Printing',
		'PPC 405', 'Digital Electronics & Microprocessors ',
		// Third Year - SEM V
		'PPC 501', 'Plastics Processing & Conversion Technologies',
		'PPC 502', 'Gravure Printing',
		'PPC 503', 'Ancillary Packaging Materials',
		'PPC 504', 'Theory of Machines & Design',
		'PPC 505', 'Instrumentation & Process Control',
		'PPS 501', 'Communication & Corporate Skills',
		// Third Year - SEM VI
		'PPC 601', 'Packaging Machineries & Systems',
		'PPC 602', 'Food & Pharmaceutical Packaging',
		'PPC 603', 'Industrial Products Packaging',
		'PPC 604', 'Flexographic Printing',
		'PPE 601X', 'Elective - I',
		'PPL 601', 'Package Design & Graphics',
		'PPS 601', 'Industrial Visits ',
		'PPE 6011', 'Packaging Distribution Dynamics',
		'PPE 6012', 'Inks & Coatings',
		'PPE 6013', 'Digital & Security Printing',
		'PPE 6014', 'Print Finishing & Converting',

		// Production: PE
		// Second Year - Sem III
		'PEC 301', 'Applied Mathematics-III',
		'PEC 302', 'Strength of Materials',
		'PEC 303', 'Manufacturing Engineering-I',
		'PEC 304', 'Fluid Mechanics and Fluid Power',
		'PEL 305', 'Computer Aided MachineDrawing+',
		'PEL 306', 'Data Base Information Retrieval System',
		'PEL 307', 'Workshop Practice-III ',
		// Second Year - Sem IV
		'PEC 401', 'Applied Mathematics-IV',
		'PEC 402', 'Theory of Machines',
		'PEC 403', 'Manufacturing Engineering-II',
		'PEC 404', 'Electrical and Electronics Engineering',
		'PEC 405', 'Applied Thermodynamics',
		'PEC 406', 'Materials Technology',
		'PEL 407', 'Workshop Practice-IV ',
		// Third Year - SEM V
		'PEC 501', 'Computer Aided Design and Finite Element Analysis',
		'PEC 502', 'Metrology and Instrumentation',
		'PEC 503', 'Design of Jigs and Fixtures',
		'PEC 504', 'Machining Science and Technology',
		'PEC 505', 'Engineering Design',
		'PEC 506', 'Thermal Engineering',
		'PEL 501', 'Business Communication and Ethics#',
		// Third Year - SEM VI
		'PEC 601', 'Process Engineering and Tooling',
		'PEC 602', 'Design of Press Tool and Metal Joining',
		'PEC 603', 'Operations Research',
		'PEC 604', 'Mould and Metal Forming Technology',
		'PEC 605', 'Production and Operations Management',
		'PEC 606', 'Machine Tool Design',
		'PEP 701', 'Industrial Training and Project ',
		'PEC 801', 'Automation and Control Engineering',
		'PEC 802', 'Computer Aided Manufacturing',
		'PEC 803', 'Engineering Economics, Finance, Accounting and Costing',
		'PEC 804', 'Total Quality Strategy',
		'PEC 805', 'Industrial relations and Human Resource Management',
		'PEE 801X', 'Elective-I',
		'PEE 8011', 'Sales and Marketing Management',
		'PEE 8012', 'Logistics and Supply Chain Management',
		'PEE 8013', 'Plastics Engineering',
		'PEE 8014', 'Entrepreneurship Development',
		'PEE 8015', 'World Class Manufacturing',
		'PEE 8016', 'Mechatronics',
		'PEE 8017', 'Industrial Robotics',
		'PEE 8018', 'Product Design and Development',
		'PEE 8019', 'Sustainable Engineering',
		'PEE 80110', 'Maintenance Engineering',

		// Mechanical Engineering: ME
		// Second Year - Sem III
		'MEC 301', 'Applied Mathematics III',
		'MEC 302', 'Thermodynamics',
		'MEC 303', 'Strength of Materials',
		'MEC 304', 'Production Process- I ',
		'MEL 305', 'Computer Aided M/c Drawing',
		'MEL 306', 'Data Base & Information Retrieval System',
		'MEL 307', 'Machine Shop Practice- I ',
		// Second Year - Sem IV
		'MEC 401', 'Applied Mathematics IV',
		'MEC 402', 'Fluid Mechanics',
		'MEC 403', 'Theory of Machines- I ',
		'MEC 404', 'Production Process- II',
		'MEC 405', 'Material Technology',
		'MEC 406', 'Industrial Electronics',
		'MEL 407', 'Machine Shop Practice- II',
		// Third Year - SEM V
		'MEC 501', 'I C Engines',
		'MEC 502', 'Mechanical Measurements and Control',
		'MEC 503', 'Production Process-III',
		'MEC 504', 'Theory of Machines- II',
		'MEC 505', 'Heat Transfer',
		'MEL 5O1', 'Business Communication and Ethics',
		// Third Year - SEM VI
		'MEC 601', 'Metrology and Quality Engineering',
		'MEC 602', 'Machine Design I',
		'MEC 603', 'Mechanical Vibrations',
		'MEC 604', 'Thermal and Fluid Power Engineering',
		'MEC 605', 'Mechatronics',
		'MEC 606', 'Finite Element Analysis',

		'MEC 701', 'Machine Design -II',
		'MEC 702', 'CAD/CAM/CAE',
		'MEC 703', 'Mechanical Utility Systems',
		'MEC 704', 'Production Planning and Control',
		'MEE 701X', 'Elective- I',
		'MEP 701', 'Project- I',
		'MEC 801', 'Design of Mechanical Systems',
		'MEC 802', 'Industrial Engineering and Management',
		'MEC 803', 'Refrigeration and Air Conditioning',
		'MEE 802X', 'Elective- II',
		'MEP 802', 'Project- II',

		'MEE 7011', 'Product Life Cycle Management (PLM)',
		'MEE 7012', 'Power Plant Engineering',
		'MEE 7013', 'Energy Management',
		'MEE 7014', 'Supply Chain Management',
		'MEE 7015', 'Computational Fluid Dynamics',
		'MEE 7016', 'Advanced Turbo Machinery',
		'MEE 7017', 'Piping Engineering',
		'MEE 7018', 'Emission and Pollution Control',
		'MEE 7019', 'Operations Research',
		'MEE 70110', 'Total Productive Maintenance (TPM)',
		'MEE 70111', 'Robotics',
		'MEE 70112', 'Digital Prototyping for Product Design –I',
		'MEE 8021', 'Micro Electro Mechanical Systems (MEMS)',
		'MEE 8022', 'Renewable Energy Sources',
		'MEE 8023', 'Project Management',
		'MEE 8024', 'Business Process Reengineering',
		'MEE 8025', 'Cryogenics',
		'MEE 8026', 'Automobile Engineering',
		'MEE 8027', 'Process Equipment Design',
		'MEE 8028', 'Alternative Fuels',
		'MEE 8029', 'Enterprise Resource Planning',
		'MEE 80210', 'World Class Manufacturing',
		'MEE 80211', 'Nanotechnology',
		'MEE 80212', 'Digital Prototyping for Product Design –II',
	);
}
?>