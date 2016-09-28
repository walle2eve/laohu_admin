<?php
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$numbers = $_POST['num'];
		$n_arr = array();
		
		$n_k = 0;
		
		foreach($numbers as $key=>$val){
			$val = intval($val);
			if($val > 0){
				$tmp_a = array_fill($n_k,$val,$key);
				$n_arr = array_merge($n_arr,$tmp_a);
			}
		}
		print_r(implode(',',get_rand_number($n_arr))) ;
	}
	
	
	function get_rand_number($numbers = array()) {
			
		shuffle($numbers);
		/**
		foreach($numbers as $key=>$val){
			if($key > 0 && $val == $numbers[$key-1]){
				get_rand_number($numbers);
				break;
			}
		}
		**/
		return $numbers;
		//return $new_numbers;  
	} 
?>
<html><br /><br /><br />
<form method="post">
1:<input type="text" name="num[1]"><br /><br />
2:<input type="text" name="num[2]"><br /><br />
3:<input type="text" name="num[3]"><br /><br />
4:<input type="text" name="num[4]"><br /><br />
5:<input type="text" name="num[5]"><br /><br />
6:<input type="text" name="num[6]"><br /><br />
7:<input type="text" name="num[7]"><br /><br />
8:<input type="text" name="num[8]"><br /><br />
9:<input type="text" name="num[9]"><br /><br />
10:<input type="text" name="num[10]"><br /><br />
11:<input type="text" name="num[11]"><br /><br />
12:<input type="text" name="num[12]"><br /><br />
13:<input type="text" name="num[13]"><br /><br />
<input type="submit"><br />
</form>
</html>	