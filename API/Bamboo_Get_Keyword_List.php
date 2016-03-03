<?php
/*
--------------------------------------
1. 설명  : 키워드 리스트 페이지 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20151213
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$keyword = "";
$m_uuid = "";	//유저 코드

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid']) || !isset($_REQUEST['keyword']) || empty($_REQUEST['keyword'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$keyword = trim($_REQUEST['keyword']);
	$m_uuid	= trim($_REQUEST['uuid']);
}

try {
	$sql_select = "select xx.* ";
	$sql_select .= "	, (select count(*) from tbl_board_like b where b.b_code = xx.b_code) as '조아요갯수' ";
	$sql_select .= "	, (select count(*) from tbl_board_like where b_code = xx.b_code and m_uuid = '".$m_uuid."') as '좋아요여부' ";
	$sql_select .= "	, (select count(*) ";
	$sql_select .= "		from tbl_board_comment b ";
	$sql_select .= "		where b.b_code = xx.b_code) as '댓글갯수' ";
	$sql_select .= "	, ifnull((select group_concat(keyword, '') from tbl_keyword where b_code = xx.b_code), '') as '키워드' ";
	$sql_select .= "	 from  ";
	$sql_select .= "	( ";
	$sql_select .= "	select ";
	$sql_select .= "		a.b_code ";
	$sql_select .= "		, a.b_contents as '내용' ";
	$sql_select .= "	    , a.regdt as '등록일' ";
	$sql_select .= "	    , a.img_url as '이미지경로' ";
	$sql_select .= "	    , a.mov_url as '동영상경로' ";	
	$sql_select .= "	from tbl_univ_board a left outer join tbl_keyword b  ";
	$sql_select .= "	on a.b_code = b.b_code ";
	$sql_select .= "	where 1=1 and b.keyword like '%".$keyword."%' ";
	$sql_select .= "	union all ";
	$sql_select .= "	select  ";
	$sql_select .= "		a.b_code ";
	$sql_select .= "		, a.b_contents as '내용' ";
	$sql_select .= "	    , a.regdt as '등록일' ";
	$sql_select .= "	    , a.img_url as '이미지경로' ";
	$sql_select .= "	    , a.mov_url as '동영상경로' ";	
	$sql_select .= "	from tbl_general_board a left outer join tbl_keyword b ";
	$sql_select .= "	on a.b_code = b.b_code ";
	$sql_select .= "	where 1=1 and b.keyword like '%".$keyword."%' ";
	$sql_select .= "	) xx ";
	$sql_select .= "	order by xx.등록일 desc ";

	$rs = mysqli_query($connect, $sql_select);

	if(!$rs) {
		throw new Exception("error with query -1", 1);
	} 

	$json_array = array();

	while ($row = mysqli_fetch_assoc($rs)) {
		$b_code = $row['b_code'];
		$b_contents = $row['내용'];
		$regdt = $row['등록일'];
		$img_url = $row['이미지경로'];
		$mov_url = $row['동영상경로'];
		$like_cnt = $row['조아요갯수'];
		$comment_cnt = $row['댓글갯수'];
		$keyword = $row['키워드'];
		$is_like = $row['좋아요여부'];

		$json = array (
			'b_code' => $b_code,
			'b_contents' => $b_contents,
			'regdt' => $regdt,
			'img_url' => $img_url,
			'mov_url' => $mov_url,
			'comment_cnt' => $comment_cnt,
			'like_cnt' => $like_cnt,
			'keyword' => $keyword,
			'is_like' => $is_like
		);
		array_push($json_array, $json);
	}

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