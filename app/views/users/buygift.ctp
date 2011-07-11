<div id="leftcolumn_user" class="bodycopy" style="overflow:auto;">
    <div class="lightbox_content_header">
	    <div>
			<div style="float:right;width:50%;text-align:left">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $html->image('https://graph.facebook.com/'.$remote_user->id.'/picture', array('width'=>50,'height'=>50)); ?>
			</div>
    		<div style="float:left;width:50%;text-align:right">
				Buying a gift for <? echo $remote_user->name; ?><br />
				<? if ($remote_user->birthday){ ?>
				<? if ($remote_user->gender=='female'): ?>
			    	Her
    			<? else: ?>
    				His
    			<? endif; ?>
   				birthday is on <? echo $remote_user->birthday; ?>
                <? } ?>
			</div>
    		<div class="clear"></div>
    		<div style="width:100%">
			<? 
				if (sizeof($remote_user_likes->data)){
				echo "Page: <br>";
				for ($page_counter = 0;$page_counter<ceil(sizeof($remote_user_likes->data)/10);$page_counter++){
					if ($page_counter == $page) echo $page;
					else {
						echo $html->link($page_counter, array('controller'=>'users','action'=>'buygift',$remote_user->id,$page_counter)); 
					}
					if ($page_counter != ceil(sizeof($remote_user_likes->data)/10)-1) echo ' | ';
				}
				echo '<br>';
				}
	 		?>			
    		</div>
    	</div>
    </div>
	    <br /><br /><br /><br /><br />
	<div class="lightbox_content_results">
	<table>
	<? 
		//$results_counter = 0;
		//for ($counter = $page*10;$counter<($page*10+10);$counter++){
		for ($results_counter = 0;$results_counter<sizeof($display_array);$results_counter++){	
	?> 	
    	<th><td>
		<?		
				echo '<hr>';
				echo $display_array[$results_counter]->name;
				echo '&nbsp;&nbsp;&nbsp;'.$html->image('https://graph.facebook.com/'.$display_array[$results_counter]->id.'/picture',array('width'=>50,'height'=>50));
				echo '<hr>';
		?>
        </td></th>
        	<?	$limit = (sizeof($results[$results_counter]) > 5) ? 5 : sizeof($results[$results_counter]);
				for ($inner_counter=0;$inner_counter<$limit;$inner_counter++){
		?>	
        <tr><td>
        			<? 
                    if (isset($results[$results_counter][$inner_counter]->SmallImage->URL)){
						echo $html->image($results[$results_counter][$inner_counter]->SmallImage->URL);
					}?>
        </td>
        <td>
					<?	
					if (isset($results[$results_counter][$inner_counter]->ItemAttributes->Title)){
						echo '<br>Title: '.$results[$results_counter][$inner_counter]->ItemAttributes->Title;
					}
					if (isset($results[$results_counter][$inner_counter]->ItemAttributes->Author)){
						echo '<br>Author: '.$results[$results_counter][$inner_counter]->ItemAttributes->Author;
					}
					if (isset($results[$results_counter][$inner_counter]->OfferSummary->LowestNewPrice->FormattedPrice)){
						echo '<br>Price: '.$results[$results_counter][$inner_counter]->OfferSummary->LowestNewPrice->FormattedPrice;
					}
					echo $html->link('Buy Now',$results[$results_counter][$inner_counter]->DetailPageURL);
					echo "<br>";
				}?>	
        </td></tr>        			
		<?
				// $results_counter++;
		}
	?>
    </table>
	</div>
</div>