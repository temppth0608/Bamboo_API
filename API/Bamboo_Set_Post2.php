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

$type = $_REQUEST['type'];
$uuid = $_REQUEST['uuid'];
$contents = $_REQUEST['contents'];
$notice = $_REQUEST['notice'];
$univ = $_REQUEST['univ'];
echo $keyword = $_REQUEST['keyword'];

echo $keyword;
$keywordArr = explode('#' , $keyword);
$keywordArrCnt = count($keywordArr);

try {
    if ($type == "T01") {
        $sql_select = "SELECT concat('G00000', ifnull(max(substring(b_code, -6))+1, '000001')) AS b_code FROM tbl_general_board;";
        $rs = mysqli_query($connect, $sql_select);
        $row = mysqli_fetch_assoc($rs);
        $b_code = $row['b_code'];
        $select_insert = "INSERT INTO `bamboo`.`tbl_general_board` (`b_code`, `m_uuid`, `b_contents`, `regdt`, `img_url`, `mov_url`, `b_blind_yn`) VALUES ('".$b_code."' ,'".$uuid."', '".$contents."', date_format(now(),'%Y%m%d%H%i%s'), '', '', 'N'); ";
    } else if ($type == "T02") {
        $sql_select = "SELECT concat('U00000', ifnull(max(substring(b_code, -6))+1, '000001')) AS b_code FROM tbl_univ_board; ";
        $rs = mysqli_query($connect, $sql_select);
        $row = mysqli_fetch_assoc($rs);
        $b_code = $row['b_code'];
        $select_insert = "INSERT INTO `bamboo`.`tbl_univ_board` 
            (`b_code`, `m_uuid`, `b_contents`,`regdt`,`img_url`, `mov_url`, `b_blind_yn`, `b_univ`, `b_notice_yn`, `b_notice_date`) 
            VALUES ('".$b_code."', '".$uuid."', '".$contents."',date_format(now(),'%Y%m%d%H%i%s') ,'', '', 'N', '".$univ."', '".$notice."', ''); ";
    }
    $rs = mysqli_query($connect, $select_insert);

    if(!$rs) {
        throw new Exception("error with query -1", 1);
    } else {
        $json = array (
        'state'   => '1',
        );
        echoJson($json);
    }

    for ($i=1; $i<$keywordArrCnt; $i++) {
        echo $keywordArr[$i];
        $sql_insert = "INSERT INTO `bamboo`.`tbl_keyword` (`b_code`, `keyword`) VALUES ('".$b_code."', '".$keywordArr[$i]."'); ";
        $rs = mysqli_query($connect, $sql_insert);
        if(!$rs) {
            throw new Exception("error with query -2", 1);
        } else {
            $json = array (
                'state'   => '1',
            );
            echoJson($json);
        }
    }

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