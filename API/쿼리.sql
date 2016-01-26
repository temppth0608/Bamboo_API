select /*확성기최신*/
	xx.b_code as 'b_code'
	, xx.b_contents as '댓글내용'
	, xx.regdt as '등록일'
	, xx.img_url as '이미지경로'
	, xx.mov_url as '동영상경로'
	, (select count(*) from tbl_board_like where b_code = xx.b_code) as '좋아요갯수'
	, (select count(*) from tbl_board_comment where b_code = xx.b_code) as '댓글갯수'

from
(
	select *
		from tbl_general_board
)xx
where 1=1
	and xx.b_blind_yn = 'N'
order by xx.regdt desc
limit 0, 50;


select /*확성기최신*/
	xx.b_code as 'b_code'
	, xx.b_contents as '댓글내용'
	, xx.regdt as '등록일'
	, xx.img_url as '이미지경로'
	, xx.mov_url as '동영상경로'
	, (select count(*) from tbl_board_like where b_code = xx.b_code) as '좋아요갯수'
	, (select count(*) from tbl_board_comment where b_code = xx.b_code) as '댓글갯수'
	, (select group_concat(keyword, SEPARATOR ':') from tbl_keyword where b_code = xx.b_code) as '키워드'
from
(
	select *
		from tbl_univ_board
)xx
where 1=1
	and xx.b_blind_yn = 'N'
order by xx.regdt desc
limit 0, 50;


select /*확성기최신*/
	xx.b_code as 'b_code'
	, xx.b_contents as '댓글내용'
	, xx.regdt as '등록일'
	, xx.img_url as '이미지경로'
	, xx.mov_url as '동영상경로'
	, (select count(*) from tbl_board_like where b_code = xx.b_code) as '좋아요갯수'
	, (select count(*) from tbl_board_comment where b_code = xx.b_code) as '댓글갯수'
from
(
	select *
		from tbl_univ_board
)xx
where 1=1
	and xx.b_blind_yn = 'N'
	and xx.b_notice_yn = 'Y'
order by xx.regdt desc
limit 0, 50;
