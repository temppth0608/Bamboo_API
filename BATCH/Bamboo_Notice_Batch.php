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

//DB연결 해제
mysqli_close($connect);
?>