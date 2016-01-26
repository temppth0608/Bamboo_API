<?php
/*
--------------------------------------
1. 설명  : 디테일 페이지 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20151210
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$b_code = "";	//게시물 code

if(!isset($_REQUEST['b_code']) || empty($_REQUEST['b_code'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$b_code	= trim($_REQUEST['b_code']);
}

try {
	$sql_select = "select ";
	$sql_select .= "	xx.b_code as '코드'";
	$sql_select .= "	,xx.b_contents as '내용' ";
	$sql_select .= "	,xx.regdt as '등록일' ";
	$sql_select .= "	,xx.img_url as '이미지경로' ";
	$sql_select .= "	,xx.mov_url as '동영상경로' ";
	$sql_select .= "	,(select count(*) from tbl_board_comment where b_code = xx.b_code) as '총댓글갯수' ";
	$sql_select .= "	,(select count(*) from tbl_board_like where b_code = xx.b_code) as '글좋아요갯수' ";
	$sql_select .= "from ";
	$sql_select .= "( ";
	$sql_select .= "select  ";
	$sql_select .= "	b_code ";
	$sql_select .= "    , b_contents ";
	$sql_select .= "    , regdt ";
	$sql_select .= "    , img_url ";
	$sql_select .= "    , mov_url ";
	$sql_select .= "from tbl_general_board ";
	$sql_select .= "where b_blind_yn = 'N' ";
	$sql_select .= "union all ";
	$sql_select .= "select  ";
	$sql_select .= "	b_code ";
	$sql_select .= "    , b_contents ";
	$sql_select .= "    , regdt ";
	$sql_select .= "    , img_url ";
	$sql_select .= "    , mov_url ";
	$sql_select .= "from tbl_univ_board "; 
	$sql_select .= "where b_blind_yn = 'N' ";
	$sql_select .= ")xx ";
	$sql_select .= "where xx.b_code = '".$b_code."'; ";

	$rs = mysqli_query($connect, $sql_select);

	if(!$rs) {
		throw new Exception("error with query -1", 1);
	}

	$json_array = array();

	$row = mysqli_fetch_assoc($rs);
	$b_code = $row['코드'];
	$b_contents = $row['내용'];
	$regdt = $row['등록일'];
	$img_url = $row['이미지경로'];
	$mov_url = $row['동영상경로'];
	$comment_cnt = $row['총댓글갯수'];
	$board_like_cnt = $row['글좋아요갯수'];

	$json = array (
		'b_code' => $b_code,
		'b_contents' => $b_contents,
		'regdt' => $regdt,
		'img_url' => $img_url,
		'mov_url' => $mov_url,
		'comment_cnt' => $comment_cnt,
		'board_like_cnt' => $board_like_cnt
	);
	array_push($json_array, $json);

	$sql_select = "select xx.* ";
	$sql_select .= "		, (select count(*) from tbl_comment_like where bc_index = xx.idx) as '댓글좋아요갯수' ";
	$sql_select .= "from ";
	$sql_select .= "( ";
	$sql_select .= "select ";
	$sql_select .= " 	idx";
	$sql_select .= " 	, regdt as '등록일' ";
	$sql_select .= " 	, comment as '댓글' ";
	$sql_select .= "from tbl_board_comment where b_code = '".$b_code."' ";
	$sql_select .= ")xx; ";
	
	$rs = mysqli_query($connect, $sql_select);

	if(!$rs) {
		throw new Exception("error with query -2", 1);
	}

	$json_temp = array();

	while ($row = mysqli_fetch_assoc($rs)) {
		$json = array();
		$regdt = $row['등록일'];
		$comment = $row['댓글'];
		$comment_like_cnt = $row['댓글좋아요갯수'];

		$json = array (
			'regdt' => $regdt,
			'comment' => $comment,
			'comment_like_cnt' => $comment_like_cnt
		);
		array_push($json_temp, $json);
	}

	array_push($json_array, $json_temp);

	$sql_select = "select keyword as '키워드' from tbl_keyword where b_code = '".$b_code."'; ";

	$rs = mysqli_query($connect, $sql_select);

	if(!$rs) {
		throw new Exception("error with query -3", 1);
	}

	$json_temp_2 = array();
	
	while ($row = mysqli_fetch_assoc($rs)) {
		$json = array();
		$keyword = $row['키워드'];

		$json = array (
			'keyword' => $keyword,
		);
		array_push($json_temp_2, $json);
	}

	array_push($json_array, $json_temp_2);
	echoJson($json_array);

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