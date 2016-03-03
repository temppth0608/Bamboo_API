<?php
/*
--------------------------------------
1. 설명  : 디바이스 토큰 가져오기  
2. 작성자 : 박태현
3. 작성일 : 20160224
4. 수정일 :
--------------------------------------
 */
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid = "";
$m_device_token = "";

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid']) || !isset($_REQUEST['deviceToken']) || empty($_REQUEST['deviceToken'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
	$m_device_token	= trim($_REQUEST['deviceToken']);
}

try {
	$sql_update = "update tbl_member set m_device_token = '".$m_device_token."' where m_uuid = '".$m_uuid."'";
	$rs = mysqli_query($connect, $sql_update);
	if(!$rs) {
		throw new Exception("error with query -1", 1);
	} 
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