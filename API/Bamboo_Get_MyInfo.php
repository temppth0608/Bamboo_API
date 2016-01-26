<?php
/*
--------------------------------------
1. 설명  : 기본 개인 정보를 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20151208
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid'])) {
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
	$sql_select = " select /*개인정보 뿌려주는 쿼리*/ ";
	$sql_select .= "		m_uuid as '코드' ";
    $sql_select .= "		, m_point as '포인트' ";
    $sql_select .= "		, m_univ as '대학' ";
	$sql_select .= " from tbl_member ";
	$sql_select .= " where 1=1 and m_uuid = '".$m_uuid."'; ";

	$rs = mysqli_query($connect, $sql_select);
	if(!$rs) {
		throw new Exception("error with query", 1);
	}

	$row = mysqli_fetch_assoc($rs);

	if($row['코드'] == null) {
		throw new Exception("no date", 1);
	}

	$m_uuid = $row['코드'];
	$m_point = $row['포인트'];
	$m_univ = $row['대학'];

	$json = array (
		'm_uuid'   => $m_uuid,
		'm_point' =>  $m_point,
		'm_univ'   => $m_univ
	);

	echoJson($json);
} catch(Exception $e) {
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