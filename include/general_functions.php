<?php
	function checkLogin($username, $password){
		global $mysqli;	
		$isAuthenticated = false;

		$chkLogin = "select * from users where (username='" . $username . "' or email_address='" . $username . "') and password='" . $password . "' and status_id = 1";
		$rsLogin = mysqli_query($mysqli, $chkLogin);
		$row = mysqli_fetch_assoc($rsLogin);

		if (!empty($row)) {
			$insertLogs = "insert into login_logs(user_id,remarks,status_id) values(" . $row['id'] . ",'Successful', 1)";
			$rsLogs = mysqli_query($mysqli, $insertLogs);
			if ($rsLogs !== false) {
				if ($row['disable_login_failure'] == 0) {
					$upUsers = "update users set failed_login_attempt = 0 where id = " . $row['id'];
					$rsUpUsers = mysqli_query($mysqli, $upUsers);
				}
				
				$_SESSION['authId'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['userType'] = $row['user_type_id'];
			}
			$isAuthenticated = true;
		} else {
			$insertLogs = "insert into login_logs(ip_address,remarks,status_id) values('" . getClientIp() . "','Failed', 2)";
			$rsLogs = mysqli_query($mysqli, $insertLogs);
		}

		return $isAuthenticated;
	}

	function randomPassword() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    $length = 10;
	    for ($i = 0; $i < $length; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	function randomActivationCode() {
	    $alphabet = '1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    $length = 6;
	    for ($i = 0; $i < $length; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	function sendViaSemaphore($mobile, $message){
		$parameters = array(
			'apikey' => '8850815abd71634b42f382b5a02ac7d6',
		    //'apikey' => 'b72b4e690594d982c5b56fe6ee4270ab',
		    'number' => $mobile,
		    'message' => $message,
		    'sendername' => 'SEMAPHORE'
		);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL,'http://api.semaphore.co/api/v4/messages' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec( $ch );
		curl_close ($ch);

		return json_decode($output);
	}

	function sendEmail() {
		$message = 'Someone trying to force a login. IP Address is <b>' . getClientIp() . '</b>';
		$adminEmail = 'doodledummy617@gmail.com';
		return phpMailer($adminEmail, "Brute-force Attack", $message);
	}

	function phpMailer($to, $subject, $message) {
		require 'PHPMailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'doodledummy617@gmail.com';                 // SMTP username
		$mail->Password = 'Password123$';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->setFrom('doodledummy617@gmail.com', 'iNnounce');
		$mail->addAddress($to);     // Add a recipient
		//$mail->addReplyTo('info@example.com', 'Information');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $message;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {
		    //echo 'Message could not be sent.';
		    //echo 'Mailer Error: ' . $mail->ErrorInfo;
		    return false;
		} else {
		    //echo 'Message has been sent';
		    return true;
		}
	}

	function getClientIp() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';

	    //$ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	    // if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
	    //   $ip = $_SERVER['HTTP_CLIENT_IP'];
	    // } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
	    // 	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    // } else {
	    //   $ip = $_SERVER['REMOTE_ADDR'];
	    // }
	    // return $ip;
	}

	function insertActivation($userId, $activationCode) {
		global $mysqli;

		$insertActivation = "insert into account_activations(user_id, activation_key) values(".$userId.", '".$activationCode."')";
		$rsActivation = mysqli_query($mysqli, $insertActivation);

		if ($rsActivation !== false) {

		}
	}

	//echo phpMailer();

	function getStudentReceivers($section) {
		global $mysqli;

		$selStud = "select students.first_name, students.last_name, students.mobile_number, year_sections.section from students inner join year_sections on students.year_section_id = year_sections.id where students.year_section_id = " . $section;
		$rsStud = mysqli_query($mysqli, $selStud);

		$data = array();
		while($studNumber = mysqli_fetch_assoc($rsStud)) {
			$studData = array(
					'name' => '[' . $studNumber['last_name'] . ',' . $studNumber['first_name'] . '(' . $studNumber['section'] . ')]',
					'mobile_number' => $studNumber['mobile_number']
					);
			array_push($data, $studData);
		}

		return $data;
	}

	function getProfReceivers($id) {
		global $mysqli;

		$selProf = "select user_infos.first_name, user_infos.last_name, user_infos.mobile_number from users inner join user_infos on users.id = user_infos.user_id where users.id = " . $id;
		$rsProf = mysqli_query($mysqli, $selProf);

		$data = array();
		while($profNumber = mysqli_fetch_assoc($rsProf)) {
			$profData = array(
					'name' => '[' . $profNumber['last_name'] . ',' . $profNumber['first_name'] . '(Prof)]',
					'mobile_number' => $profNumber['mobile_number']
					);
			array_push($data, $profData);
		}

		return $data;
	}
?>