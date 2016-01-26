<?php
/*
--------------------------------------
1. 설명  : 중간에 대학명 변경 api
2. 작성자 : 박태현
3. 작성일 : 20151210
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값
$m_univ	 = ""; 		//대학명

if (!isset($_REQUEST['uuid']) || !isset($_REQUEST['university']) || empty($_REQUEST['uuid']) || empty($_REQUEST['university'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
	$m_univ	= trim($_REQUEST['university']);
}

try {
	$sql_update = "UPDATE `bamboo`.`tbl_member` SET `m_univ`='".$m_univ."' WHERE `m_uuid`='".$m_uuid."'; ";

	$rs = mysqli_query($connect, $sql_update);
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