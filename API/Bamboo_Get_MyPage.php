<?php
/*
--------------------------------------
1. 설명  : My Page 데이터 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20151209
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$m_uuid	 = "";		//기기 고유값
$type	 = "";		//'T01' = 글, 'T02' = 댓글

if(!isset($_REQUEST['uuid']) || empty($_REQUEST['uuid']) || !isset($_REQUEST['type']) || empty($_REQUEST['type'])) {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter'
	);
	echoJson($json);
	exit();
} else {
	$m_uuid	= trim($_REQUEST['uuid']);
	$type	= trim($_REQUEST['type']);
}

if ($type == "T01") {
	try {	
		$sql_select = "select xx.* ";
		$sql_select .= "	, (select count(*) ";
		$sql_select .= "		from tbl_board_like b ";
		$sql_select .= "		where b.b_code = xx.b_code) as '조아요갯수' ";
		$sql_select .= "	, (select count(*) ";
		$sql_select .= "		from tbl_board_comment b ";
		$sql_select .= "		where b.b_code = xx.b_code) as '댓글갯수' ";
		$sql_select .= " 	, ifnull((select group_concat(keyword, '') from tbl_keyword where b_code = xx.b_code), '') as '키워드' ";
		$sql_select .= " from  ";
		$sql_select .= "( ";
		$sql_select .= "select  ";
		$sql_select .= "	'대학' as '타입' ";
		$sql_select .= "	, b_contents as '내용' ";
		$sql_select .= "	, date_format(regdt, '%y/%m/%d') as '날짜' ";
		$sql_select .= "	, date_format(regdt,'%a') as '요일' ";
		$sql_select .= "	, date_format(regdt,'%H:%i %p') as '시간' ";
		$sql_select .= "	, b_notice_yn as '확성기여부' ";
		$sql_select .= "    , b_code ";
		$sql_select .= " from tbl_univ_board ";
		$sql_select .= "where 1=1 and m_uuid = '".$m_uuid."' ";
		$sql_select .= "union all ";
		$sql_select .= "select   ";
		$sql_select .= "	'일반' as '타입' ";
		$sql_select .= "	, b_contents as '내용' ";
		$sql_select .= "	, date_format(regdt, '%y/%m/%d') as '날짜' ";
		$sql_select .= "	, date_format(regdt,'%a') as '요일' ";
		$sql_select .= "	, date_format(regdt,'%H:%i %p') as '시간' ";
		$sql_select .= "	, 'N' as '확성기여부' ";
		$sql_select .= "    , b_code ";
		$sql_select .= "from tbl_general_board ";
		$sql_select .= "where 1=1 and m_uuid = '".$m_uuid."' ";
		$sql_select .= ") xx ";
		$sql_select .= "order by xx.b_code desc; ";

		$rs = mysqli_query($connect, $sql_select);

		if(!$rs) {
			throw new Exception("error with query", 1);
		}

		$json_array = array();

		while ($row = mysqli_fetch_assoc($rs)) {

			if($row['b_code'] == null) {
				throw new Exception("no data", 1);
			}
			$json = array();

			$type = $row['타입'];
			$b_contents = $row['내용'];
			$date = $row['날짜'];
			$day = $row['요일'];
			$time = $row['시간'];
			$b_notice_yn = $row['확성기여부'];
			$b_code = $row['b_code'];
			$like_cnt = $row['조아요갯수'];
			$comment_cnt = $row['댓글갯수'];
			$keyword = $row['키워드'];

			$json = array (
				'b_code' => $b_code,
				'type' => $type,	
				'b_contents' => $b_contents,	
				'date' => $date,	
				'day' => $day,	
				'time' => $time,	
				'b_notice_yn' => $b_notice_yn,		
				'like_cnt' => $like_cnt,	
				'comment_cnt' => $comment_cnt,
				'keyword' => $keyword
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
} else if ($type == "T02") {
	try {
		$sql_select = "select ";
		$sql_select .= "	xx.* ";
		$sql_select .= "	, (select count(*) from tbl_comment_like where bc_index = xx.idx) as '댓글좋아요갯수' ";
		$sql_select .= " from ";
		$sql_select .= " ( ";
		$sql_select .= " select  ";
		$sql_select .= " 	 idx ";
		$sql_select .= " 	, b_code as '코드' ";
		$sql_select .= "	, date_format(regdt, '%y/%m/%d') as '날짜' ";
		$sql_select .= "	, date_format(regdt,'%a') as '요일' ";
		$sql_select .= "	, date_format(regdt,'%H:%i %p') as '시간' ";
		$sql_select .= " 	, comment as '댓글내용' ";
		$sql_select .= " from tbl_board_comment ";
		$sql_select .= " where m_uuid = '".$m_uuid."' ";
		$sql_select .= " order by regdt desc ";
		$sql_select .= " )xx; ";
		
		$rs = mysqli_query($connect, $sql_select);

		if(!$rs) {
			throw new Exception("error with query", 1);
		}

		$json_array = array();

		while ($row = mysqli_fetch_assoc($rs)) {
			if($row['idx'] == null) {
				throw new Exception("no data", 1);
			}
			$json = array();

			$b_code = $row['코드'];
			$date = $row['날짜'];
			$day = $row['요일'];
			$time = $row['시간'];
			$comment = $row['댓글내용'];
			$comment_like_cnt = $row['댓글좋아요갯수'];

			$json = array (
				'b_code' => $b_code,
				'date' => $date,
				'day' => $day,
				'time' => $time,
				'comment' => $comment,
				'comment_like_cnt' => $comment_like_cnt
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
} else {
	$json = array (
		'state'   => '0',
		'message' => 'error with parameter(type)'
	);
	echoJson($json);
	exit();
}

//DB연결 해제
mysqli_close($connect);
?>