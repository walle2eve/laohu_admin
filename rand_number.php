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
<?php
for($i=0;$i<=12;$i++){
	?>
	<?php echo $i;?>:<input type="text" name="num[<?php echo $i;?>]"><br /><br />
	<?php
}
?>
<input type="submit"><br />
</form>
</html>	