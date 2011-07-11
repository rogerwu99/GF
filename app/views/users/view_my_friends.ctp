<div id="leftcolumn_user" class="bodycopy" style="overflow:auto;">
     <!--Alphabetical | Birthday-->				
    <br /><br />
<? if ($limit==48) {
		echo '0';
	}
	else {
		echo $html->link('0',array('controller'=>'users','action'=>'view_my_friends',48));
	}
	
    for ($page = 1; $page < (sizeof($friends) / 48); $page++) {
		$upper_bound = ($page+1) * 48; 
		$lower_bound = $upper_bound - 48;
	//	$string = ;
		
		if ($upper_bound == $limit){
			echo ' | '.$page; 
		}
		else {
			echo ' | '.$html->link($page,array('controller'=>'users','action'=>'view_my_friends',$upper_bound));
		}
	}
	?>
    <div class='clear'></div><Br /><Br />
	<div style="height:1500px;width:800px;text-align:left">
<? for ($counter = $start; $counter<$limit; $counter++){
		  if ($counter!=$start && ($counter) % 3 == 0) echo '<br>'; 
?>
<span class="smallercopy_nav" style="width:25%;border-style:none;float:left;border-width:none;text-align:left;"><?	echo $html->link($friends[$counter]->name,array('controller'=>'users','action'=>'buygift',$friends[$counter]->id,0));
	?>&nbsp;&nbsp;&nbsp; <?		echo $html->image('http://graph.facebook.com/'.$friends[$counter]->id.'/picture', array('width'=>30, 'height'=>30)); ?>
           </span>		
	<?	}
	?>
    
</div>
</div>