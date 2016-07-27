<?php 
namespace Admin\Model;
use Think\Model;

class LoginLogStatModel extends Model
{
    protected $connection = 'DB_LAOHU_LOG_CONFIG';
    protected $tablePrefix = '';

	// 获取运营商
	public function get_login_stat_by_operator($operator_id,$start_date = ''){

		$and_where = " WHERE  operator_id =  " . $operator_id;
		if(strtotime($start_date)) $and_where .= "  AND login_date >=  '".date('Y-m-d',strtotime($start_date))."'";

		return  M()->table("(
		SELECT uid,MAX(lianxu_days) lianxu_days FROM (
				SELECT
					uid,
					max(days) lianxu_days,
					min(login_day) start_date,
					max(login_day) end_date,
					cont_ix
				FROM
					(
						SELECT
							uid ,@cont_day := (
								CASE
								WHEN (
									@last_uid = uid
									AND DATEDIFF(login_dt ,@last_dt) = 1
								) THEN
									(@cont_day + 1)
								WHEN (
									@last_uid = uid
									AND DATEDIFF(login_dt ,@last_dt) < 1
								) THEN
									(@cont_day + 0)
								ELSE
									1
								END
							) AS days,
							(
								@cont_ix := (
									@cont_ix +
									IF (@cont_day = 1, 1, 0)
								)
							) AS cont_ix ,@last_uid := uid ,@last_dt := login_dt login_day
						FROM
							(
								SELECT
									operator_id,
									account_id AS uid,
									login_date AS login_dt
								FROM
									laohu_log.login_log_stat
								" . $and_where . "
								ORDER BY
									uid,
									login_dt
							) AS t,
							(
								SELECT
									@last_uid := '' ,@last_dt := '' ,@cont_ix := 0 ,@cont_day := 0
							) AS t1
					) AS t2
				GROUP BY
					uid,
					cont_ix) t3 GROUP BY uid)")->alias('t4')->field('count(t4.uid) as login_count,sum(case when lianxu_days >= 2 then 1 else 0 end) day_2_num,sum(case when lianxu_days >= 3 then 1 else 0 end) day_3_num,sum(case when lianxu_days >= 7 then 1 else 0 end) day_7_num,sum(case when lianxu_days >= 15 then 1 else 0 end) day_15_num,sum(case when lianxu_days >= 30 then 1 else 0 end) day_30_num,sum(case when lianxu_days >= 90 then 1 else 0 end) day_90_num')->find();

	}
}