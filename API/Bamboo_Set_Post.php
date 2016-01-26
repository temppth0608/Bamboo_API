<?php
/*
--------------------------------------
1. 설명  : 개시글 포스트
2. 작성자 : 박태현
3. 작성일 : 20160108
4. 수정일 :
--------------------------------------
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/func.php');

// get picture variables
$file       = $_FILES['file']['tmp_name'];
$fileName   = $_FILES['file']['name'];
$fileType   = $_FILES['file']['type'];

// check extension
$allowedExts = array("jpg", "jpeg", "png");
$rootName = reset(explode(".", $fileName));
$extension = end(explode(".", $fileName));

// create new file name
$time = time();
$newName = $rootName.$time.'.'.$extension;

// temporarily save file
$moved = move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/".$newName );
if ($moved) $path = "uploads/".$newName;

$type = $_POST['type'];
$uuid = $_POST['uuid'];
$contents = $_POST['contents'];
$notice	= $_POST['notice'];
$univ = $_POST['univ'];
$keyword = $_POST['keyword'];

$keywordArr = explode('#' , $keyword);
$keywordArrCnt = count($keywordArr);

$time = time();

if ($moved) {
    $fullUrl = "http://ec2-52-68-50-114.ap-northeast-1.compute.amazonaws.com/bamboo/API/".$path;
    $arrayToSend = array (
    	'status'=>'success'
    );
    if ($type == "T01") {
    	$sql_select = "SELECT concat('G00000', ifnull(max(substring(b_code, -6))+1, '000001')) AS b_code FROM tbl_general_board;";
    	$rs = mysqli_query($connect, $sql_select);
		$row = mysqli_fetch_assoc($rs);
		$b_code = $row['b_code'];
    	$select_insert = "INSERT INTO `bamboo`.`tbl_general_board` (`b_code`, `m_uuid`, `b_contents`, `regdt`, `img_url`, `mov_url`, `b_blind_yn`) VALUES ('".$b_code."' ,'".$uuid."', '".$contents."', date_format(now(),'%Y%m%d%H%i%s'), '".$fullUrl."', '', 'N'); ";
    } else if ($type == "T02"){
    	$sql_select = "SELECT concat('U00000', ifnull(max(substring(b_code, -6))+1, '000001')) AS b_code FROM tbl_univ_board; ";
    	$rs = mysqli_query($connect, $sql_select);
		$row = mysqli_fetch_assoc($rs);
		$b_code = $row['b_code'];
    	$select_insert = "INSERT INTO `bamboo`.`tbl_univ_board` 
			(`b_code`, `m_uuid`, `b_contents`,`regdt`,`img_url`, `mov_url`, `b_blind_yn`, `b_univ`, `b_notice_yn`, `b_notice_date`) 
			VALUES ('".$b_code."', '".$uuid."', '".$contents."',date_format(now(),'%Y%m%d%H%i%s') ,'".$fullUrl."', '', 'N', '".$univ."', '".$notice."', ''); ";
    }
    $rs = mysqli_query($connect, $select_insert);

    for ($i=0; $i<$keywordArrCnt; $i++) {
        $sql_insert = "INSERT INTO `bamboo`.`tbl_keyword` (`b_code`, `keyword`) VALUES ('".$b_code."', '".$keywordArr[$i]."'); ";
        $rs = mysqli_query($connect, $sql_insert);
    }
} else {
    $arrayToSend = array (
    	'status'=>'FAILED'
    );
}

//header('Content-Type:application/json');
echoJson($arrayToSend);

//DB연결 해제
mysqli_close($connect);
?>