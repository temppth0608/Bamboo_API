<?php
/*
--------------------------------------
1. 설명  : 디테일 페이지 댓글
2. 작성자 : 박태현
3. 작성일 : 20160107
4. 수정일 :
--------------------------------------
 */
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid = "";	//유저 코드
$b_code = "";	//게시물 코드

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid']) || !isset($_REQUEST['b_code']) || empty($_REQUEST['b_code'])) {
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
	$sql_select = "SELECT ";
	$sql_select .= " a.regdt as '등록일' ";
	$sql_select .= " , a.comment as '댓글내용' ";
	$sql_select .= " , (select count(1) from tbl_comment_like where a.idx = bc_index and m_uuid = '".$m_uuid."') as '좋아요여부' ";
	$sql_select .= " , (select count(1) from tbl_comment_like where a.idx = bc_index) as '댓글좋아요갯수' ";
	$sql_select .= " FROM tbl_board_comment a ";
	$sql_select .= " where b_code = '".$b_code."' ";
	$sql_select .= " order by regdt desc; ";

	$rs = mysqli_query($connect, $sql_select);
	if(!$rs) {
		throw new Exception("error with query -1", 1);
	} 

	$json_array = array();

	while ($row = mysqli_fetch_assoc($rs)) {
		$regdt = $row['등록일'];
		$comment = $row['댓글내용'];
		$isLike = $row['좋아요여부'];
		$commentLikeCnt = $row['댓글좋아요갯수'];

		$json = array (
			'regdt' => $regdt,
			'comment' => $comment,
			'isLike' => $isLike,
			'commentLikeCnt' => $commentLikeCnt
		);
		array_push($json_array, $json);
	}
	echoJson($json_array);
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