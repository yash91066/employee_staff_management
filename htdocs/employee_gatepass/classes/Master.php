<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_department(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `department_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Department Name already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `department_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `department_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New Department successfully saved.";
				$id = $this->conn->insert_id;
			}else{
				$res['msg'] = "Department successfully updated.";
			}
		$this->settings->set_flashdata('success',$res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_department(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `department_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Department successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_designation(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `designation_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Designation already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `designation_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `designation_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Designation successfully saved.");
			else
				$this->settings->set_flashdata('success',"Designation successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_designation(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `designation_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Designation  successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_employee(){
		if(empty($_POST['id'])){
			$prefix = date("Y");
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `employee_list` where employee_code ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['employee_code'] = $prefix."-".$code;
		}
		$_POST['fullname'] = ucwords($_POST['lastname'].', '.$_POST['firstname'].' '.$_POST['middlename']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('employee_code','department_id','designation_id','fullname','status'))){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `employee_list` set {$data}";
		}else{
			$sql = "UPDATE `employee_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
			$employee_id = $this->conn->insert_id;
			else
			$employee_id = $id;
			$resp['id'] = $employee_id;
			$data = "";
			foreach($_POST as $k =>$v){
				if(in_array($k,array('id','employee_code','department_id','designation_id','fullname','status')))
				continue;
				if(!empty($data)) $data .=", ";
				$data .= "('{$employee_id}','{$k}','{$v}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `employee_meta` where employee_id = '{$employee_id}'");
				$sql2 = "INSERT INTO `employee_meta` (`employee_id`,`meta_field`,`meta_value`) VALUES {$data}";
				$save = $this->conn->query($sql2);
				if(!$save){
					$resp['status'] = 'failed';
					if(empty($id)){
						$this->conn->query("DELETE FROM `employee_list` where id '{$employee_id}'");
					}
					$resp['msg'] = 'Saving Employee Details has failed. Error: '.$this->conn->error;
					$resp['sql'] = 	$sql2;
				}
			}
			if(isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name'] != ''){
				$fname = 'uploads/employee-'.$employee_id.'.png';
				$dir_path =base_app. $fname;
				$upload = $_FILES['avatar']['tmp_name'];
				$type = mime_content_type($upload);
				$allowed = array('image/png','image/jpeg');
				if(!in_array($type,$allowed)){
					$resp['msg'].=" But Image failed to upload due to invalid file type.";
				}else{
					$new_height = 200; 
					$new_width = 200; 
			
					list($width, $height) = getimagesize($upload);
					$t_image = imagecreatetruecolor($new_width, $new_height);
					imagealphablending( $t_image, false );
					imagesavealpha( $t_image, true );
					$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
					imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					if($gdImg){
							if(is_file($dir_path))
							unlink($dir_path);
							$uploaded_img = imagepng($t_image,$dir_path);
							imagedestroy($gdImg);
							imagedestroy($t_image);
					}else{
					$resp['msg'].=" But Image failed to upload due to unkown reason.";
					}
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Employee was successfully added.");
			}else{
				$this->settings->set_flashdata('success'," Employee's Details Successfully updated.");
			}
		}

		return json_encode($resp);
	}
	function delete_employee(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `employee_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Employee Details Successfully deleted.");
			if(is_file(base_app.'uploads/employee-'.$id.'.png'))
			unlink(base_app.'uploads/employee-'.$id.'.png');
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	// function log_employee(){
	// 	extract($_POST);
	// 	$qry = $this->conn->query("SELECT * from employee_list where employee_code = '{$employee_code}'");
	// 	if($qry->num_rows > 0){
	// 		$res = $qry->fetch_array();
	// 		$sql = "INSERT INTO `logs` set employee_id = '{$res['id']}', `type` = '{$type}'";
	// 		$save = $this->conn->query($sql);
	// 		if($save){
	// 			$resp['status'] = 'success';
	// 			if($type == 1){
	// 			$resp['title'] = 'Sucessfully Logged In';
	// 			$resp['msg'] = 'Welcome '. $res['fullname'];
	// 		}else{
	// 			$resp['title'] = 'Sucessfully Logged Out';
	// 			$resp['msg'] = 'Goodbye '. $res['fullname'];
	// 		}
	// 		}else{
	// 			$resp['status'] = 'failed';
	// 			$resp['title'] = 'Logging Error';
	// 			$resp['msg'] = '';
	// 		}
	// 	}else{
	// 		$resp['status'] = 'failed';
	// 		$resp['title'] = 'Unknown Employee Code';
	// 		$resp['msg'] = '';
	// 	}
	// 	return json_encode($resp);
	// }

	function log_employee() {
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM employee_list WHERE employee_code = '{$employee_code}'");
	
		if ($qry->num_rows > 0) {
			$res = $qry->fetch_array();
			$employee_id = $res['id'];
	
			// Check the latest log for this employee
			$latest_log = $this->conn->query("SELECT * FROM logs WHERE employee_id = '{$employee_id}' ORDER BY date_created DESC LIMIT 1")->fetch_array();
	
			if ($type == 1) {
				// Employee wants to log in
				if (!$latest_log || $latest_log['type'] == 2) {
					// The employee has not logged in yet or has logged out, so proceed to log in
					$sql = "INSERT INTO logs (employee_id, type) VALUES ('{$employee_id}', '{$type}')";
					$save = $this->conn->query($sql);
					if ($save) {
						$resp['status'] = 'success';
						$resp['title'] = 'Successfully Logged In';
						$resp['msg'] = 'Welcome ' . $res['fullname'];
					} else {
						$resp['status'] = 'failed';
						$resp['title'] = 'Logging Error';
						$resp['msg'] = '';
					}
				} else {
					// The employee is already logged in
					$resp['status'] = 'failed';
					$resp['title'] = 'Already Logged In';
					$resp['msg'] = 'You are already logged in.';
				}
			} else {
				// Employee wants to log out
				if ($latest_log && $latest_log['type'] == 1) {
					// The employee is logged in, so proceed to log out
					$sql = "INSERT INTO logs (employee_id, type) VALUES ('{$employee_id}', '{$type}')";
					$save = $this->conn->query($sql);
					if ($save) {
						$resp['status'] = 'success';
						$resp['title'] = 'Successfully Logged Out';
						$resp['msg'] = 'Goodbye ' . $res['fullname'];
					} else {
						$resp['status'] = 'failed';
						$resp['title'] = 'Logging Error';
						$resp['msg'] = '';
					}
				} else {
					// The employee is not logged in or has already logged out
					$resp['status'] = 'failed';
					$resp['title'] = 'Not Logged In';
					$resp['msg'] = 'Please log in first.';
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['title'] = 'Unknown Employee Code';
			$resp['msg'] = '';
		}
		return json_encode($resp);
	}
	

	
	// function log_visitor(){
	// 	extract($_POST);
	// 	$data = "";
	// 	foreach($_POST as $k => $v){
	// 		if(!is_numeric($v))
	// 		$v = $this->conn->real_escape_string($v);
	// 		if(!empty($data)) $data .= ", ";
	// 		$data .= " `{$k}` = '{$v}' ";
	// 	}
	// 	$sql = "INSERT INTO `visitor_logs` set {$data}";
	// 	$save = $this->conn->query($sql);
	// 	if($save){
	// 		$resp['status'] = 'success';
	// 		if($type == 1){
	// 		$resp['title'] = 'Sucessfully Logged In';
	// 		$resp['msg'] = 'Welcome '. $name;
	// 	}else{
	// 		$resp['title'] = 'Sucessfully Logged Out';
	// 		$resp['msg'] = 'Goodbye '. $name;
	// 	}
	// 	}else{
	// 		$resp['status'] = 'failed';
	// 		$resp['title'] = 'Logging Error';
	// 	}
	// 	return json_encode($resp);
	// }

	function log_visitor() {
		extract($_POST);
		
		// Input validation
		if (empty($name) || empty($contact) || empty($address) || empty($purpose)) {
			$resp['status'] = 'failed';
			$resp['title'] = 'Missing Information';
			$resp['msg'] = 'Please fill in all the required fields (name, contact, address, purpose).';
			return json_encode($resp);
		}
	
		// Check if the visitor has a previous log
		$qry = $this->conn->query("SELECT * FROM visitor_logs WHERE contact = '{$contact}' ORDER BY date_created DESC LIMIT 1");
		if ($qry->num_rows > 0) {
			$latest_log = $qry->fetch_assoc();
	
			if ($type == 1) {
				// Visitor wants to log in
				if ($latest_log['type'] == 1) {
					// The visitor is already logged in
					$resp['status'] = 'failed';
					$resp['title'] = 'Already Logged In';
					$resp['msg'] = 'You are already logged in.';
					return json_encode($resp);
				} elseif ($latest_log['type'] == 2) {
					// The visitor has logged out, so proceed to log in
					$sql = "INSERT INTO visitor_logs (name, contact, address, purpose, type) VALUES ('$name', '$contact', '$address', '$purpose', '$type')";
					$save = $this->conn->query($sql);
					if ($save) {
						$resp['status'] = 'success';
						$resp['title'] = 'Successfully Logged In';
						$resp['msg'] = 'Welcome ' . $name;
					} else {
						$resp['status'] = 'failed';
						$resp['title'] = 'Logging Error';
						$resp['msg'] = 'An error occurred while logging in. Please try again later.';
					}
					return json_encode($resp);
				}
			} elseif ($type == 2) {
				// Visitor wants to log out
				if ($latest_log['type'] == 2) {
					// The visitor has already logged out
					$resp['status'] = 'failed';
					$resp['title'] = 'Already Logged Out';
					$resp['msg'] = 'You are already logged out.';
					return json_encode($resp);
				} elseif ($latest_log['type'] == 1) {
					// The visitor is logged in, so proceed to log out
					$sql = "INSERT INTO visitor_logs (name, contact, address, purpose, type) VALUES ('$name', '$contact', '$address', '$purpose', '$type')";
					$save = $this->conn->query($sql);
					if ($save) {
						$resp['status'] = 'success';
						$resp['title'] = 'Successfully Logged Out';
						$resp['msg'] = 'Goodbye ' . $name;
					} else {
						$resp['status'] = 'failed';
						$resp['title'] = 'Logging Error';
						$resp['msg'] = 'An error occurred while logging out. Please try again later.';
					}
					return json_encode($resp);
				}
			}
		} else {
			// No previous log found, so check the type
			if ($type == 2) {
				// If the visitor has not logged in, they cannot log out
				$resp['status'] = 'failed';
				$resp['title'] = 'Not Logged In';
				$resp['msg'] = 'Please log in first.';
				return json_encode($resp);
			} else {
				// Proceed to log in
				$sql = "INSERT INTO visitor_logs (name, contact, address, purpose, type) VALUES ('$name', '$contact', '$address', '$purpose', '$type')";
				$save = $this->conn->query($sql);
				if ($save) {
					$resp['status'] = 'success';
					$resp['title'] = 'Successfully Logged In';
					$resp['msg'] = 'Welcome ' . $name;
				} else {
					$resp['status'] = 'failed';
					$resp['title'] = 'Logging Error';
					$resp['msg'] = 'An error occurred while logging in. Please try again later.';
				}
				return json_encode($resp);
			}
		}
	}
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_department':
		echo $Master->save_department();
	break;
	case 'delete_department':
		echo $Master->delete_department();
	break;
	case 'save_designation':
		echo $Master->save_designation();
	break;
	case 'delete_designation':
		echo $Master->delete_designation();
	break;
	case 'get_designation':
		echo $Master->get_designation();
	break;
	case 'save_employee':
		echo $Master->save_employee();
	break;
	case 'delete_employee':
		echo $Master->delete_employee();
	break;
	case 'log_employee':
		echo $Master->log_employee();
	break;
	case 'log_visitor':
		echo $Master->log_visitor();
	break;
	default:
		// echo $sysset->index();
		break;
}