<?php
/*
--------------------------------------
1. 설명  : 최초 앱 실행시 기기 고유 값 및 대학 이름 insert하는 파일
2. 작성자 : 박태현
3. 작성일 : 20151207
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
	$sql_select = "SELECT count(*) as 'cnt' FROM tbl_member where m_uuid = '".$m_uuid."' ";
	$rs = mysqli_query($connect, $sql_select);
	$row = mysqli_fetch_assoc($rs);
	if($row['cnt'] == "1") {
		throw new Exception("error with duplicated primary key", 1);
	}
} catch(Exception $e) {
	$json = array (
		'state'   => '0',
		'message' =>  $e->getMessage()
	);
	echoJson($json);
	exit();
}

try {
	$sql_insert = " INSERT INTO `bamboo`.`tbl_member` ";
	$sql_insert .= " (`m_uuid`, `regdt`, `m_blind_yn`, `m_point`, `m_univ`) ";
	$sql_insert .= " VALUES";
	$sql_insert .= " ('".$m_uuid."', date_format(now(),'%Y%m%d%H%i%s'), 'N', '0', '".$m_univ."'); ";

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