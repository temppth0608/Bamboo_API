<?php
/*
--------------------------------------
1. 설명  : 대학별 핫 키워드 리스트 페이지 뿌려주는 api
2. 작성자 : 박태현
3. 작성일 : 20160218
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

try {
	$sql_select = "select /*일반글 키워드 갯수대로 리스팅*/ ";
	$sql_select .= "	keyword as '키워드' ";
	$sql_select .= "    , count(*) as 'count' ";
	$sql_select .= "from tbl_keyword ";
	$sql_select .= "where b_code like '%U%' ";
	$sql_select .= "group by keyword ";
	$sql_select .= "order by count desc ";
	$sql_select .= "limit 0, 5; ";

	$rs = mysqli_query($connect, $sql_select);

	if(!$rs) {
		throw new Exception("error with query -1", 1);
	} 

	$json_array = array();

	while ($row = mysqli_fetch_assoc($rs)) {
		$keyword = $row['키워드'];
		$count 	 = $row['count'];

		$json = array (
			'keyword' => $keyword,
			'count'	  => $count 
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