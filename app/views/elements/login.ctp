<?php if(!empty($_Auth['User'])): 
	
    $logged_in = $this->requestAction('/users/loggedin/'.$this->params['action']);
//    var_dump( $logged_in );
	?>
    <span class="bodycopy">
	<span class="sidebar5" id="logged_in">
	<span class="sidebar_desktop_adjustment">
		<?php 
			if ($_Auth['User']['fb_pic_url']!=''):  
				echo $html->image($_Auth['User']['fb_pic_url'], array('alt' => 'Pic', 'width' => 50, 'height' => 50, 'class' => 'top', 'align'=>'left'));
			endif; 
		?>
			<? if ($this->params['action']=='view_my_friends'){
					echo 'My Friends';
				}
				else {	
					echo $html->link('My Friends', array('controller'=>'users','action'=>'view_my_friends/48')); 
				}
				?>
                <!--<strong> | </strong>
                --><? //if ($this->params['action']=='edit'){
					//echo 'Settings';
				//}
				//else {	
					//echo $html->link('Settings', array('controller'=>'users', 'action'=>'edit'));
				//}
				?>	
				
				<strong>|</strong>
			<?php echo $html->link('Sign Out', array('controller'=>'users', 'action'=>'logout')); ?>
            <br /><span="smallercopy_cap">This will sign you out of Facebook.</span>
		<? endif; ?>		
	</span>
	
	</span>
    
	</span>
<div class="clear"></div>
