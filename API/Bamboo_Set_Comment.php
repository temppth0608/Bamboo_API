<?php
/*
--------------------------------------
1. 설명  : 댓글 달기 API
2. 작성자 : 박태현
3. 작성일 : 20151210
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값
$b_code	 = ""; 		//게시글 코드
$comment = "";		//댓글 내용

if (!isset($_REQUEST['uuid']) || !isset($_REQUEST['b_code']) || empty($_REQUEST['uuid']) || empty($_REQUEST['b_code']) || !isset($_REQUEST['comment'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
	$b_code	= trim($_REQUEST['b_code']);
	$comment = trim($_REQUEST['comment']);
}

try {
	if($b_code[0] == "G") {
		$sql_select = "select m_uuid from tbl_general_board where b_code = '".$b_code."' ";
	} else if($b_code[0] == "U") {
		$sql_select = "select m_uuid from tbl_univ_board where b_code = '".$b_code."' ";
	}
	$rs = mysqli_query($connect, $sql_select);
	if (!$rs) {
		throw new Exception("error with database -1", 1);
	}
	$row = mysqli_fetch_assoc($rs);

	$selected_m_uuid = $row['m_uuid'];

	$sql_select = "select m_point from tbl_member where m_uuid = '".$selected_m_uuid."' ";
	$rs = mysqli_query($connect, $sql_select);
	if (!$rs) {
		throw new Exception("error with database -2", 1);
	}
	$row = mysqli_fetch_assoc($rs);

	$selected_m_point = $row['m_point'];

	$sql_update = "update tbl_member set m_point = '".$selected_m_point."' + 1 where m_uuid = '".$selected_m_uuid."'";
	$rs = mysqli_query($connect, $sql_update);
	if (!$rs) {
		throw new Exception("error with database -3", 1);
	}

	if ($selected_m_uuid != $m_uuid) {
		/*-------------------Push Apns-------------------*/
		$sql_select2 = "select m_device_token from tbl_member where m_uuid = '".$selected_m_uuid."' ";
		$rs = mysqli_query($connect, $sql_select2);
		if (!$rs) {
			throw new Exception("error with database -1", 1);
		}
		$row = mysqli_fetch_assoc($rs);
	}
	$device_token = $row['m_device_token'];
	$alert = "속닥 게시물에 댓글이 달렸습니다. :]";

	pushApns($device_token, $alert);
	/*----------------------------------------------*/

	$sql_insert = "INSERT INTO `bamboo`.`tbl_board_comment` ";
	$sql_insert .= 		"(`b_code`, `m_uuid`, `regdt`, `comment`) ";
	$sql_insert .= "	VALUES ('".$b_code."', '".$m_uuid."', date_format(now(),'%Y%m%d%H%i%s'), '".$comment."'); ";

	$rs = mysqli_query($connect, $sql_insert);
	if (!$rs) {
		throw new Exception("error with database -4", 1);
	}
    $json = array (
    	'state'   => '1',
    	'message' => 'success'
    );
    echoJson($json);

} catch (Exception $e) {
	$json = array (
		'state'   => '0',
		'message' =>  $e->getMessage()
	);
	echoJson($json);
	exit();
}

//DB연결 해제
mysqli_close($connect);
?>