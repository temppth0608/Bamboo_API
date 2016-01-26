<?php

require_once($_SERVER["DOCUMENT_ROOT"] . '/bamboo/config.php');

for($i=0; $i<10; $i++) {

	$sql_select = "SELECT concat('U00000', ifnull(max(substring(b_code, -6))+1, '000001')) AS b_code FROM tbl_univ_board; ";
	$rs = mysqli_query($connect, $sql_select);
	$row = mysqli_fetch_assoc($rs);
	$b_code = $row['b_code'];
	sleep(1);
	echo $sql = "INSERT INTO `bamboo`.`tbl_univ_board` (`b_code`, `m_uuid`, `b_contents`,`regdt`,`img_url`, `mov_url`, `b_blind_yn`, `b_univ`, `b_notice_yn`, `b_notice_date`)  VALUES ('".$b_code."' ,'53525105-86A6-4A2A-BC15-3404D00DF2A4', '게시글_테스트_".$i."', date_format(now(),'%Y%m%d%H%i%s'), '', '', 'N', '서경대학교', 'Y', ''); ";
	$rs = mysqli_query($connect, $sql);

	if($rs) {
		echo "1";
	} else {
		echo "0";
	}
}

?>