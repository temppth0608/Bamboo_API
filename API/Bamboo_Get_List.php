<?php
/*
--------------------------------------
1. 설명  : 리스트 페이지 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20151213
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$type = "";	//일반최신 = T01, 일반인기 = T02, 대학최신 = T03, 대학인기 = T04, 확성기 = T05
$page = "";
$univ = "";

if(!isset($_REQUEST['type']) || empty($_REQUEST['type']) || !isset($_REQUEST['page'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$type	= trim($_REQUEST['type']);
	if(empty($_REQUEST['page'])) {
		$page = 1;
	} else {
		$page	= trim($_REQUEST['page']);
	}
	$univ = trim($_REQUEST['university']);
}

if ($type == "T01" || $type == "T03" || $type == "T05") {
	$sort_type = "new";
} else {
	$sort_type = "hit";
}

if ($type == "T01" || $type == "T02") {
	$table_name = "tbl_general_board";
} else {
	$table_name = "tbl_univ_board";
}

$onePage = 20;

try {
	if ($sort_type = "new") {
		$sql_count = " select count(*) as 'cnt' from ".$table_name;
		$rs = mysqli_query($connect, $sql_count);
		$row = mysqli_fetch_assoc($rs);

		$allPost = $row['cnt'];
		$allPage = ceil($allPost / $onePage);

		if ($page < 1 && $page > $allPage) {
			throw new Exception("Error with Pageing", 1);
		}

		$currentLimit = ($onePage * $page) - $onePage;

		$sql_limit = "limit " . $currentLimit . ", " . $onePage;

		$sql_select = " select /*일반 최신*/  ";
		$sql_select .= "	xx.b_code as 'b_code' ";
		$sql_select .= "	, xx.b_contents as '게시글내용' ";
		$sql_select .= "	, xx.regdt as '등록일' ";
		$sql_select .= "	, xx.img_url as '이미지경로' ";
		$sql_select .= "	, xx.mov_url as '동영상경로' ";
		$sql_select .= "	, (select count(*) from tbl_board_like where b_code = xx.b_code) as '좋아요갯수' ";
		$sql_select .= "	, (select count(*) from tbl_board_comment where b_code = xx.b_code) as '댓글갯수' ";
		$sql_select .= " 	, ifnull((select group_concat(keyword, '') from tbl_keyword where b_code = xx.b_code), '') as '키워드' ";
		$sql_select .= " from ";
		$sql_select .= " ( ";
		$sql_select .= " 	select * ";
		$sql_select .= "		from ".$table_name;
		$sql_select .= " )xx ";
		$sql_select .= " where 1=1 ";
		$sql_select .= "	and xx.b_blind_yn = 'N' ";
		if($type == "T05") {
			$sql_select .= "and xx.b_notice_yn = 'Y' ";
		}
		if($type == "T03" || $type == "T04" || $type == "T05") {
			$sql_select .= "and b_univ = '".$univ."' ";
		}
		$sql_select .= " order by xx.regdt desc ";
		$sql_select .= $sql_limit;

		$rs = mysqli_query($connect, $sql_select);

		if(!$rs) {
			throw new Exception("error with query -1", 1);
		} 

		$json_array = array();

		while ($row = mysqli_fetch_assoc($rs)) {
			$b_code = $row['b_code'];
			$b_contents = $row['게시글내용'];
			$regdt = $row['등록일'];
			$img_url = $row['이미지경로'];
			$mov_url = $row['동영상경로'];
			$like_cnt = $row['좋아요갯수'];
			$comment_cnt = $row['댓글갯수'];
			$keyword = $row['키워드'];

			$json = array (
				'b_code' => $b_code,
				'b_contents' => $b_contents,
				'regdt' => $regdt,
				'img_url' => $img_url,
				'mov_url' => $mov_url,
				'comment_cnt' => $comment_cnt,
				'like_cnt' => $like_cnt,
				'keyword' => $keyword
			);

			array_push($json_array, $json);
		}

		echoJson($json_array);
	} else {

	}
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