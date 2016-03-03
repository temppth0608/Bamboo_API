<?php
/*
--------------------------------------
1. 설명  : 확성기 게시판 배치 파일
2. 작성자 : 박태현
3. 작성일 : 20151215
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

$today = date("YmdHis");

$sql_select = "select b_code, regdt from tbl_univ_board where b_notice_yn = 'Y' ";
$rs = mysqli_query($connect, $sql_select);

while ($row = mysqli_fetch_assoc($rs)) {
	$b_code = $row['b_code'];
	$regdt = $row['regdt'];

	
}
//DB연결 해제
mysqli_close($connect);
?>