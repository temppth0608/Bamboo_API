<?php
/*
--------------------------------------
1. 설명  : 디테일 페이지 좋아요시 처리하는 api
2. 작성자 : 박태현
3. 작성일 : 20160107
4. 수정일 :
--------------------------------------
 */
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid = "";	//유저 코드
$bc_index = "";	//댓글 코드

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid']) || !isset($_REQUEST['idx']) || empty($_REQUEST['idx'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid		= trim($_REQUEST['uuid']);
	$bc_index	= trim($_REQUEST['idx']);
}

try {
	$sql_select = "select count(*) as 'cnt' from tbl_comment_like where bc_index = '".$bc_index."' and m_uuid = '".$m_uuid."' ";
	$rs = mysqli_query($connect, $sql_select);
	if (!$rs) {
		throw new Exception("error with database -1", 1);
	}
	$row = mysqli_fetch_assoc($rs);

	//좋아요가 눌려져있는 경우
	if ($row['cnt'] == "0") {
		$sql_insert = "insert into tbl_comment_like ";
		$sql_insert .= "(bc_index, m_uuid, regdt)";
		$sql_insert .= " values ('".$bc_index."', '".$m_uuid."', date_format(now(),'%Y%m%d%H%i%s')); ";
		$rs = mysqli_query($connect, $sql_insert);
		if (!$rs) {
			throw new Exception("error with database -3", 1);
		}
	//좋아요가 눌려져있지 않는 경우
	} else {
		$sql_delete = "delete from tbl_comment_like where bc_index = '".$bc_index."' and m_uuid = '".$m_uuid."' ";
		$rs = mysqli_query($connect, $sql_delete);
		if (!$rs) {
			throw new Exception("error with database -2", 1);
		}
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