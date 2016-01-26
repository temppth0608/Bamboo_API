<?php
/*
--------------------------------------
1. 설명  : point관련 처리
2. 작성자 : 박태현
3. 작성일 : 20151210
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값

if (!isset($_REQUEST['uuid']) ||  empty($_REQUEST['uuid'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
}

try {
	$sql_insert = "select m_point from tbl_member where m_uuid = '".$m_uuid."' ";
	$rs = mysqli_query($connect, $sql_insert);
	if (!$rs) {
		throw new Exception("error with database-1", 1);
	}
	$row = mysqli_fetch_assoc($rs);

	$selected_m_point = $row['m_point'];

	$sql_update = "update tbl_member set m_point = '".$selected_m_point."' + 10 where m_uuid = '".$m_uuid."'";
	$rs = mysqli_query($connect, $sql_update);
	if (!$rs) {
		throw new Exception("error with database-2", 1);
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