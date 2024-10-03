<?php 
if(session_id() ==="")
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class Master extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function save_visitor(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['visitor-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            if(empty($visitor_id)){
                $sql = "INSERT INTO `visitor_list` (`user_id`, `id_number`, `fullname`, `contact`, `email`, `reason`, `remarks`) VALUES ('{$user_id}', '{$id_number}', '{$fullname}', '{$contact}', '{$email}', '{$reason}', '{$remarks}')";
            }else{
                $sql = "UPDATE `visitor_list` set `id_number` = '{$id_number}', `fullname` = '{$fullname}', `contact` = '{$contact}', `email` = '{$email}', `reason` = '{$reason}', `remarks` = '{$remarks}' where `visitor_id` = '{$visitor_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($visitor_id))
                $resp['msg'] = 'New Visitor has been addedd successfully';
                else
                $resp['msg'] = 'Visitor Details has been updated successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_visitor(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['visitors'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `visitor_list` where `visitor_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The visitor data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_comment(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['comment-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            if(empty($comment_id)){
                $sql = "INSERT INTO `comment_list` (`user_id`, `visitor_id`, `comment`) VALUES ('{$user_id}', '{$visitor_id}', '{$comment}')";
            }else{
                $sql = "UPDATE `comment_list` set `comment` = '{$comment}' where `comment_id` = '{$comment_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($comment_id))
                $resp['msg'] = 'New comment has been addedd successfully';
                else
                $resp['msg'] = 'Comment Data has been updated successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_comment(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['visitorDetails'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `comment_list` where `comment_id` = '{$comment_id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The comment data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function exit_visitor(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['visitors'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $update = $this->query("UPDATE `visitor_list` set `date_out` = CURRENT_TIMESTAMP where `visitor_id` = '{$id}'");
            if($update){
                $resp['status'] = 'success';
                $resp['msg'] = 'Visitor has been marked as exited successfully.';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
        
    }

    function today_visitors(){
        $from = date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 00:00:00"));
        $to =  date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 23:59:59"));
        $from = new DateTime($from, new DateTimeZone('America/Toronto'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('America/Toronto'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");
    
        $total = $this->querySingle("SELECT COUNT(`visitor_id`) FROM `visitor_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}'");
        return number_format($total);
    }
    
    function today_visitors_not_exited(){
        $from = date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 00:00:00"));
        $to =  date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 23:59:59"));
        $from = new DateTime($from, new DateTimeZone('America/Toronto'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('America/Toronto'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");
    
        $total = $this->querySingle("SELECT COUNT(`visitor_id`) FROM `visitor_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' and `date_out` IS NULL");
        return number_format($total);
    }
    
    function today_visitors_exited(){
        $from = date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 00:00:00"));
        $to =  date("Y-m-d H:i:s", strtotime(date("Y-m-d"). " 23:59:59"));
        $from = new DateTime($from, new DateTimeZone('America/Toronto'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('America/Toronto'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");
    
        $total = $this->querySingle("SELECT COUNT(`visitor_id`) FROM `visitor_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' and `date_out` IS NOT NULL");
        return number_format($total);
    }
    

}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$master = new Master();
switch($a){
    case 'save_settings':
        echo $master->save_settings();
    break;
    case 'save_visitor':
        echo $master->save_visitor();
    break;
    case 'delete_visitor':
        echo $master->delete_visitor();
    break;
    case 'save_comment':
        echo $master->save_comment();
    break;
    case 'delete_comment':
        echo $master->delete_comment();
    break;
    case 'exit_visitor':
        echo $master->exit_visitor();
    break;
    default:
    // default action here
    break;
}