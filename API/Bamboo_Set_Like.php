<?php
/*
--------------------------------------
1. 설명  : 좋아요 처리 API
2. 작성자 : 박태현
3. 작성일 : 20151210
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값
$b_code	 = ""; 		//대학명

if (!isset($_REQUEST['uuid']) || !isset($_REQUEST['b_code']) || empty($_REQUEST['uuid']) || empty($_REQUEST['b_code'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
	$b_code	= trim($_REQUEST['b_code']);
}

try {
	$sql_select = "select count(*) as 'cnt'";
	$sql_select .= "		from tbl_board_like where b_code = '".$b_code."' and  ";
	$sql_select .= "		m_uuid = '".$m_uuid."'; ";

	$rs = mysqli_query($connect, $sql_select);
	$row = mysqli_fetch_assoc($rs);
	if($row['cnt'] == "1") {
		throw new Exception("warning with same board like", 1);
	}

	if($b_code[0] == "G") {
		$sql_select = "select m_uuid from tbl_general_board where b_code = '".$b_code."' ";
	} else if($b_code[0] == "U") {
		$sql_select = "select m_uuid from tbl_univ_board where b_code = '".$b_code."' ";
	}
	$sql_select;
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

	$sql_insert = "INSERT INTO `bamboo`.`tbl_board_like` ";
	$sql_insert .= "	(`b_code`, `m_uuid`, `regdt`)  ";
	$sql_insert .= "	VALUES ('".$b_code."', '".$m_uuid."', date_format(now(),'%Y%m%d%H%i%s')); ";

	$rs = mysqli_query($connect, $sql_insert);
	if (!$rs) {
		throw new Exception("error with database", 1);
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