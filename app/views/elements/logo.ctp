	  <h1 id="branding">
			<? 
				if(!empty($_Auth['User']['start_date'])):
						 echo $html->link("Gift Finder", 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/merchants/dashboard'); 
				else:
						 echo $html->link("Gift Finder", 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']); 
				endif;
						 echo $html->image('default.jpg', array('alt'=>'Moo Cow','width'=>75,'height'=>60,'class'=>'top'));?>
		</h1>
			  
	
