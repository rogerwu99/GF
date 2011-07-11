<?php
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth2.php'));

class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Ajax');
	var $components = array('Auth', 'Email','Paypal','Session');
	var $uses = array('User');
	
	function index()
	{
		if(is_null($this->Auth->getUserId())){
       		Controller::render('/deny');
        }
		else {
			$this->redirect(array('controller'=>'users','action'=>'view_my_friends/48'));
		}
	}

	function _login($username=null, $password=null)
	{
		if ($username && $password){
			$user_record_1=array();
			$user_record_1['Auth']['username']=$username;
			$user_record_1['Auth']['password']=$password;
			$this->Auth->authenticate_from_oauth($user_record_1['Auth']);
			return;		
		}
	}
	
	function login()
	{
		$this->_login($this->data['Auth']['username'],$this->Auth->hasher($this->data['Auth']['password']));
		if ($this->Session->check('hash_value')){
			$this->redirect(array('controller'=>'beta','action'=>'index',$this->Session->read('hash_value')));
		}
		else {
			$this->redirect(array('action'=>'view_my_profile'));
		}
	}
	
	function register($step=null)
	{
		/*if (!empty($this->data)){
			$email = $this->data['User']['email'];
			$password = $this->data['User']['new_password'];
			$confirm =$this->data['User']['confirm_password'];
			$accept = $this->data['User']['accept'];
			$fb_uid = $this->data['User']['fb_uid'];
			$name = $this->data['User']['name'];
			$month = $this->data['User']['smonth'];
			$date = $this->data['User']['sdate']+1;
			$year = $this->data['User']['syear'];
			$sex = $this->data['User']['sex'];
	
			$this->data=array();
			$this->User->create();
			$this->data['User']['name'] = $name;
			$this->data['User']['email'] = (string) $email;
			$this->data['User']['new_password']=$password;
			$this->data['User']['confirm_password']=$confirm;
			$this->data['User']['accept']=$accept;
			$this->data['User']['sex']=$sex;
			$final_year = (int)date('Y')-$year-13;
			$this->data['User']['birthday']= date('Ymd',strtotime($month.' '.$date.' '.$final_year));
			$password = $this->data['User']['password'] = $this->Auth->hasher($password); 
			$username = $this->data['User']['username']= (string) $email;
			$this->data['User']['fb_pic_url']='http://graph.facebook.com/'.$fb_uid.'/picture';
			$this->data['User']['facebook_access_key'] = $this->Session->read('facebook_access_key');
			$this->data['User']['fb_uid']=$fb_uid;
			
			$this->User->set($this->data);
			if ($this->User->validates()){
				$this->User->save();
				$this->_login($username,$password);
				$this->redirect(array('action'=>'view_my_friends/50'));
			}
			else {
				$this->set('errors', $this->User->validationErrors);
				unset($this->data['User']['new_password']);
	   			unset($this->data['User']['confirm_password']);
			}
			
		}
		else {
			//$this->redirect('/');
		}*/
	}
	
	function buygift($fb_id, $page){
		// each page has 10 likes
		$user = $this->User->find('first', array('conditions' => (array('User.id'=>$this->Auth->getUserId()))));
		$remote_user = json_decode(file_get_contents('https://graph.facebook.com/'.$fb_id.'?'. $user['User']['facebook_access_key']));
		$remote_user_likes = json_decode(file_get_contents('https://graph.facebook.com/'.$fb_id.'/likes?'.$user['User']['facebook_access_key']));
	//	$control = json_decode($user['Interest']['likes']);
		//$likes = json_decode(file_get_contents('https://graph.facebook.com/'.$fb_id.'/likes?'.$user['User']['facebook_access_key']));
		$this->set('remote_user',$remote_user);
		$this->set('remote_user_likes',$remote_user_likes);
		
		$this->set('page',$page);
		
		$results = array();
		$results_counter = 0;
		$display_array = array();
		for ($counter = $page*10;$counter<($page*10+10);$counter++){	
			//$results[$results_counter]=$this->printSearchResults($this->itemsearch($remote_user_likes->data[$counter]->name));
		$data=$this->itemsearch($remote_user_likes->data[$counter]->name);
		
		if ($data->Items->Item) {
			$display_array[$results_counter]=$remote_user_likes->data[$counter];
			$results[$results_counter]=$data->Items->Item;
			$results_counter++;
			}
		}
		$this->set('display_array',$display_array);
		$this->set('results',$results);
	}
	
	
	
	
	function printSearchResults($parsed_xml){
  print("<table>");
  echo $parsed_xml;
  if($numOfItems>0){
  foreach($parsed_xml->Items->Item as $current){
    print("<td><font size='-1'><b>".$current->ItemAttributes->Title."</b>");
    if (isset($current->ItemAttributes->Title)) {
    print("<br>Title: ".$current->ItemAttributes->Title);
  } elseif(isset($current->ItemAttributes->Author)) {
    print("<br>Author: ".$current->ItemAttributes->Author);
  } elseif
   (isset($current->Offers->Offer->Price->FormattedPrice)){
    print("<br>Price:
    ".$current->Offers->Offer->Price->FormattedPrice);
  }else{
  print("<center>No matches found.</center>");
   }
  }
 }
}
	
	
	
	
	
	
		function itemsearch($Keywords){//,$SearchIndex='All'){

//Set the values for some of the parameters.
//$Keywords='harry+potter';
$Operation = "ItemSearch";
$Version = "2010-11-01";
$ResponseGroup = "ItemAttributes,Offers,Images";
//User interface provides values
//for $SearchIndex and $Keywords
$base_url = "http://ecs.amazonaws.com/onca/xml";
$url_params = array('Operation'=>$Operation,'Service'=>"AWSECommerceService",
 'AWSAccessKeyId'=>Access_Key_ID,'AssociateTag'=>Associate_tag,
 'Version'=>$Version,'Availability'=>"Available",'Condition'=>"All",
 'ItemPage'=>"1",'ResponseGroup'=>$ResponseGroup,
 'Keywords'=>$Keywords,'SearchIndex'=>$SearchIndex);
//$url = $base_url . '?' .$request_to_sign.'&Signature='.$signature;
$re_url = 'http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId='.Access_Key_ID.'&Operation=ItemSearch&Keywords='.$Keywords.'&ResponseGroup='.$ResponseGroup.'&Version='.$Version.'&AssociateTag='.Associate_tag.'&SearchIndex=All';
$url = $this->signAmazonUrl($re_url, '5REcha7Fz+g1yUI9ISZ8TQ18TmjV8qcXM9HoPBjz');
//print $url;


// Add the Timestamp
//$url_params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
 
// Sort the URL parameters
/*$url_parts = array();
foreach(array_keys($url_params) as $key)
    $url_parts[] = $key."=".$url_params[$key];
sort($url_parts);
 
// Construct the string to sign
$string_to_sign = "GET\necs.amazonaws.com\n/onca/xml\n";
$request_to_sign = implode("&",$url_parts);
$request_to_sign = str_replace('+','%20',$request_to_sign);
$request_to_sign = str_replace(':','%3A',$request_to_sign);
//$request_to_sign = str_replace('-','%7E',$request_to_sign);

$request_to_sign = str_replace(';',urlencode(';'),$request_to_sign);
 //echo $string_to_sign;
// Sign the request
$string_to_sign = $string_to_sign.$request_to_sign;
$signature = hash_hmac("sha256",$string_to_sign,'5REcha7Fz+g1yUI9ISZ8TQ18TmjV8qcXM9HoPBjz',TRUE);
 
// Base64 encode the signature and make it URL safe
$signature = base64_encode($signature);
$signature = str_replace('+','%2B',$signature);
$signature = str_replace('=','%3D',$signature);
//$signature = str_replace('-','%7E',$signature);
//$signature = urlencode($signature); 
 
$url_string = implode("&",$url_parts);
//$url = $base_url.'?'.$url_string."&Signature=".$signature;
$url = $base_url . '?' .$request_to_sign.'&Signature='.$signature;
//$url = str_replace("%7E", "~", rawurlencode($url));

print $url;
*/

$response = file_get_contents($url);
$parsed_xml = simplexml_load_string($response);
//printSearchResults($parsed_xml, $SearchIndex);
//var_dump($SearchIndex);
//var_dump($Keywords);
//var_dump($parsed_xml);
return $parsed_xml;
}
	function signAmazonUrl($url, $secret_key)
{
    $original_url = $url;

    // Decode anything already encoded
    $url = urldecode($url);

    // Parse the URL into $urlparts
    $urlparts       = parse_url($url);

    // Build $params with each name/value pair
    foreach (split('&', $urlparts['query']) as $part) {
        if (strpos($part, '=')) {
            list($name, $value) = split('=', $part, 2);
        } else {
            $name = $part;
            $value = '';
        }
        $params[$name] = $value;
    }

    // Include a timestamp if none was provided
    if (empty($params['Timestamp'])) {
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    }

    // Sort the array by key
    ksort($params);

    // Build the canonical query string
    $canonical       = '';
    foreach ($params as $key => $val) {
        $canonical  .= "$key=".rawurlencode(utf8_encode($val))."&";
    }
    // Remove the trailing ampersand
    $canonical       = preg_replace("/&$/", '', $canonical);

    // Some common replacements and ones that Amazon specifically mentions
    $canonical       = str_replace(array(' ', '+', ',', ';'), array('%20', '%20', urlencode(','), urlencode(':')), $canonical);

    // Build the sign
    $string_to_sign             = "GET\n{$urlparts['host']}\n{$urlparts['path']}\n$canonical";
    // Calculate our actual signature and base64 encode it
    $signature            = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));

    // Finally re-build the URL with the proper string and include the Signature
    $url = "{$urlparts['scheme']}://{$urlparts['host']}{$urlparts['path']}?$canonical&Signature=".rawurlencode($signature);
    return $url;
}
	function logout()
	{
		$user=$this->Auth->getUserInfo();
		//$this->Session->destroy();
		//if(!empty($session)){
//			echo $user['facebook_access_key'];
		$url = 'https://www.facebook.com/logout.php?next='.ROOT_URL.'&'.$user['facebook_access_key'].'&confirm=1';

	$this->Auth->logout($url);
		//}
		//else {
		  //  $this->Auth->logout();
		//}
	}
	 function curl_get_file_contents($URL) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
    curl_close($c);
    if ($contents) return $contents;
    else return FALSE;
  }
	function loggedin($action){
		$user = $this->Auth->getUserInfo();
		$graph_url = "https://graph.facebook.com/me?" . $user['facebook_access_key'];
//  echo $graph_url;
  $response = json_decode($this->curl_get_file_contents($graph_url));
 // var_dump($response);
  //Check for errors 
  if ($response->error) {
  // check to see if this is an oAuth error:
    if ($response->error->type== "OAuthException") {
      // Retrieving a valid access token. 
      $dialog_url= "https://www.facebook.com/dialog/oauth?client_id=228371000525970&redirect_uri=" . ROOT_URL.'/users/callback/facebook';
		
//	echo $dialog_url;
	
	$this->Session->write('check',true);
	
		$this->redirect($dialog_url);
		
    }
    else {
      echo "other error has happened";
    }
  } 
  else {
  	if ($response->id != $user['fb_uid']) $this->logout();
  
  
  }
  
	}
	
	private function createConsumer($type) {
		switch ($type) {
			case 'facebook':
				return new OAuth_Consumer('228371000525970','d630ab22f06e34cb6184a23ca6d9d579');
		}
    }
	
	function getOAuth($service=NULL){
		$consumer = $this->createConsumer($service);
		$redirect_url = '';
		switch ($service){
			case 'facebook':
				$redirect_url = 'https://www.facebook.com/dialog/oauth?client_id=228371000525970&redirect_uri='.ROOT_URL.'/users/callback/facebook'.'&scope=user_about_me,user_activities,user_birthday,user_education_history,user_events,user_groups,user_hometown,user_interests,user_relationships,user_religion_politics,user_status,user_website,user_work_history,email,user_checkins,user_likes,friends_likes,friends_interests,friends_checkins,friends_activities,friends_work_history,friends_relationship_details,friends_website,friends_religion_politics,friends_relationships,friends_location,friends_relationship_details,friends_hometown,friends_education_history,friends_birthday,friends_about_me';
				break;
		}	
		$this->redirect($redirect_url);
	}
	
	
	function callback($service=NULL){
		
		$consumer = $this->createConsumer($service);
		$requestTokenName = $service.'_request_token';
		$accessTokenName = $service.'_access_token';
		$accessKeyName = $service.'_access_key';
		$accessSecretName = $service.'_access_secret';
		$access_url = '';
			
		switch ($service){
			case 'facebook':
				$access_url = 'https://graph.facebook.com/oauth/access_token?client_id=228371000525970&redirect_uri='.ROOT_URL.'/users/callback/facebook&client_secret=d630ab22f06e34cb6184a23ca6d9d579&code='.$this->params['url']['code'];
				break;
		}
		
		$accessToken = file_get_contents($access_url);
		

		//$this->User->read(null,$this->Auth->getUserId());
		
			
	//	if ($service=='facebook'){
			$this->Session->write('facebook_access_key',$accessToken);
		//}
		//$this->User->save($this->data);
		
		
		
if ($this->Session->check('check')){
	//echo 'ion here';
	$this->User->read(null,$this->Auth->getUserId());
	$this->data['User']['facebook_access_key'] = $accessToken;
	$this->User->save($this->data);
	$this->Session->delete('check');
	
	$this->redirect(array('action'=>'view_my_friends/48'));
}
		
			$this->redirect(array('action'=>'fb_callback'));
	}
	function fb_callback(){	
	$this->layout = 'about';
		if (!empty($this->data)){
			$email = $this->data['User']['email'];
			$password = $this->data['User']['new_password'];
			$confirm =$this->data['User']['confirm_password'];
			$accept = $this->data['User']['accept'];
			$fb_uid = $this->data['User']['fb_uid'];
			$name = $this->data['User']['name'];
			$month = $this->data['User']['smonth'];
			$date = $this->data['User']['sdate']+1;
			$year = $this->data['User']['syear'];
			$sex = $this->data['User']['sex'];
	
			$this->data=array();
			$this->User->create();
			$this->data['User']['name'] = $name;
			$this->data['User']['email'] = (string) $email;
			$this->data['User']['new_password']=$password;
			$this->data['User']['confirm_password']=$confirm;
			$this->data['User']['accept']=$accept;
			$this->data['User']['sex']=$sex;
			$final_year = (int)date('Y')-$year-13;
			$this->data['User']['birthday']= date('Ymd',strtotime($month.' '.$date.' '.$final_year));
			$password = $this->data['User']['password'] = $this->Auth->hasher($password); 
			$username = $this->data['User']['username']= (string) $email;
			$this->data['User']['fb_pic_url']='http://graph.facebook.com/'.$fb_uid.'/picture';
			$this->data['User']['facebook_access_key'] = $this->Session->read('facebook_access_key');
			$this->data['User']['fb_uid']=$fb_uid;
			
			$this->User->set($this->data);
			if ($this->User->validates()){
				$this->User->save();
				$this->_login($username,$password);
				$this->redirect(array('action'=>'view_my_friends/48'));
			}
			else {
			$accessToken = $this->Session->read('facebook_access_key');
				$fb_user = json_decode(file_get_contents('https://graph.facebook.com/me?' . $accessToken));
			$this->set('fb_user',$fb_user);
				$this->set('errors', $this->User->validationErrors);
				unset($this->data['User']['new_password']);
	   			unset($this->data['User']['confirm_password']);
			}
			
		}
		else {
			$accessToken = $this->Session->read('facebook_access_key');
		$fb_user = json_decode(file_get_contents('https://graph.facebook.com/me?' . $accessToken));
		
		$db_results = $this->User->find('first', array('conditions' => (array('User.fb_uid'=>$fb_user->id))));
		
		if (empty($db_results)) {
		
			$this->set('fb_user',$fb_user);
			$this->getFacebookData();
			
			$this->render();
		}
		else {
			$this->_login($db_results['User']['username'],$db_results['User']['password']);
			$this->User->read(null,$this->Auth->getUserId());
			$this->data['User']['facebook_access_key'] = $accessToken;
			$this->User->set($this->data);
			$this->User->save();
		
			
			$this->redirect(array('action'=>'view_my_friends/48'));
		
		}}
	//	$this->redirect(array('action'=>'view_my_friends/50'));

	}
	function view_my_friends($limit){
		$user = $this->Auth->getUserInfo();
		$this->set('pic',$user['path']);
		$friend_array = $this->getFriends();
		//var_dump($friend_array);
		$this->set('friends',$friend_array);
		$this->set('start',$limit-48);
		$this->set('limit',$limit);
	}
	function getFriends(){
		$friend_array = array();
		//$count =0;
		$user = $this->Auth->getUserInfo(); 
		$friend_url = json_decode(file_get_contents('https://graph.facebook.com/me/friends?' . $user['facebook_access_key']));
		
			usort($friend_url->data, array(&$this, "friend_sort"));

		return $friend_url->data;
	}
	function friend_sort($a,$b){
		if ($a->name > $b->name) return 1;
		elseif($a->name == $b->name) return 0;
		else return -1;
	}

	/*
		
	
	function googleCallback(){
		$requestToken = $this->Session->read('google_request_token');
		$consumer = $this->createConsumer('google');
		$accessToken = $consumer->getAccessToken('https://www.google.com/accounts/OAuthGetAccessToken', $requestToken);
		$this->Session->write('google_access_token',$accessToken);
		
		$updated_id = $this->Auth->getUserId();
		$this->User->read(null,$updated_id);
		$this->data['User']['go_access_key'] =  $accessToken->key;
		$this->data['User']['go_access_secret'] =  $accessToken->secret;
		$this->User->save($this->data);
		
		$this->redirect(array('action'=>'view_my_profile'));
	}*/
	
	
	
	
	
	
	function new_data($type){
		
		$user = $this->Auth->getUserInfo();
		$db_results = $this->User->find('first', array('conditions' => (array('User.id'=>$this->Auth->getUserId()))));
			
		switch ($type){
			case 'foursquare':
				$places = $this->getFoursquareData();
				$locations = array();
				$categories = array();
				if (!empty($db_results['Place']['categories']) && !empty($db_results['Place']['locations'])){
					$locations = $db_results['Place']['locations'];
					$categories = $db_results['Place']['categories'];
				}
				list ($locations, $categories) = $this->scrub_foursquare_data($places, $locations, $categories);
				if (!empty($db_results['Place']['body'])){
					$this->data['Place']['body']=json_encode($places).$db_results['Place']['body'];
					$this->Place->read(null,$db_results['Place']['id']);
				}
				else {
					$this->data['Place']['body']=json_encode($places);
					$this->Place->create();
				}
				$this->data['Place']['locations']=json_encode($locations);
				$this->data['Place']['categories']=json_encode($categories);
					
				$this->data['Place']['user_id']=$this->Auth->getUserId();
				$this->Place->set($this->data);
				$this->Place->save();
				
				
				break;
				
				
			case 'linkedin':
				list($work,$education) = $this->getLinkedInData();
				
				$industries = (!empty($db_results['Work']['industries'])) ? $db_results['Work']['industries'] : array();
				$titles = (!empty($db_results['Work']['titles'])) ? $db_results['Work']['titles']: array(); 
				
				list ($titles,$industries) = $this->scrub_work($titles, $industries, $work);
				if (!empty($db_results['School']['body'])){
					$this->data['School']['body']=json_encode($education).$db_results['School']['body'];
					$this->School->read(null,$db_results['School']['id']);
				}
				else {
					$this->School->create();
					$this->data['School']['body']=json_encode($education);
				}
				$this->data['School']['user_id']=$this->Auth->getUserId();
				$this->School->set($this->data);
				$this->School->save();
			
				if (!empty($db_results['Work']['body'])){
					$this->data['Work']['body']=json_encode($work).$db_results['Work']['body'];
					$this->Work->read(null,$db_results['Work']['id']);
				}
				else {
					$this->Work->create();
					$this->data['Work']['body']=json_encode($work);
				}
			
				$this->data['Work']['industries']=json_encode($industries);
				$this->data['Work']['titles']=json_encode($titles);
				$this->data['Work']['user_id']=$this->Auth->getUserId();
				$this->Work->set($this->data);
				$this->Work->save();
				break;
				
			
				
			
			case 'facebook':
			
				list ($master, $fb_data, $fb_movies, $fb_user_likes) = $this->getFacebookData();
				
				//var_dump($fb_user_likes);
				
				if (empty($db_results['Userprofile']['id'])) $this->store_master_data($master);
				$this->compareToLinkedInData($master);
				if ($user['foursquare_access_token']!=''){
					$facebook_place_data = $this->normalize_facebook_data_to_foursquare($fb_data);
					//var_dump($facebook_place_data);
					if (!empty($db_results['Place']['body'])){
						$this->data['Place']['body']=$db_results['Place']['body'].json_encode($facebook_place_data);
						$this->Place->read(null,$db_results['Place']['id']);
					}
					else {
						$this->data['Place']['body']=json_encode($facebook_place_data);
						$this->Place->create();
					}
					$locations = array();
					$categories = array();
					if (!empty($db_results['Place']['categories']) && !empty($db_results['Place']['locations'])){
						$locations = json_decode($db_results['Place']['locations'], true);
						$categories = json_decode($db_results['Place']['categories'], true);
					}
					
				
					
					
					list ($locations, $categories) = $this->scrub_foursquare_data($facebook_place_data, $locations, $categories);
					// locations and categories got messed up
					
					
					$this->data['Place']['locations']=json_encode($locations);
					$this->data['Place']['categories']=json_encode($categories);
				}
				$this->data['Place']['user_id']=$this->Auth->getUserId();
				$this->Place->set($this->data);
				$this->Place->save();
				
				if (!empty($db_results['Interest']['id'])){
			
//					$tag_cloud = json_decode($db_results['Interest']['likes'],true);
	//				$tag_cloud=$this->getFacebookInterests($tag_cloud,$fb_user_likes);
					$this->Interest->read(null,$db_results['Interest']['id']);
				}
				else {
		//			$tag_cloud = $this->getFacebookInterests(array(),$fb_user_likes);
					$this->Interest->create();
				}
				$this->data['Interest']['likes']=json_encode($fb_user_likes->data);
				$this->data['Interest']['user_id']=$this->Auth->getUserId();
				$this->Interest->set($this->data);
				$this->Interest->save();
				break;
			
			case 'meetup':
				
				$interests = $this->getMeetupData();
				
				if (!empty($db_results['Interest']['id'])){
				
					$twitter_tags = json_decode($db_results['Interest']['body'],true);	
					$tag_cloud = $this->scrub_interests($twitter_tags, $interests);
					$this->Interest->read(null,$db_results['Interest']['id']);
				
				}
				else {
					$tag_cloud = $this->scrub_interests(array(), $interests);
					$this->Interest->create();
	
				}
				$this->data['Interest']['body']=json_encode($tag_cloud);
				$this->data['Interest']['user_id']=$this->Auth->getUserId();
				$this->Interest->set($this->data);
				$this->Interest->save();
				break;
			case 'twitter':
			/*
				if ($user['tw_access_key']!=''){
					$user_info = $this->scrub_data_tw($user_info);	
				}
			*/
			
			
				list ($following,$lists_members,$lists_f,$user_list) = $this->getTwitterData();
				if (!empty($db_results['Interest']['id'])){
					$meetup_tags = json_decode($db_results['Interest']['body'], true);
					$tag_cloud = $this->scrub_interests($meetup_tags, $following,$lists_f);
					$this->Interest->read(null,$db_results['Interest']['id']);
				}
				else {
					$tag_cloud = $this->scrub_interests(array(), $following,$lists_f);
					$this->Interest->create();
				}
				$rep_followers = $this->getRepresentativeFollowers($tag_cloud,$user_list,$following);
				var_dump($rep_followers);
				$you = $this->scrub_interests(array(),$lists_members);
				$this->data['Interest']['you_body']=json_encode($you);
				$this->data['Interest']['body']=json_encode($tag_cloud);
				$this->data['Interest']['user_id']=$this->Auth->getUserId();
				$this->Interest->set($this->data);
				$this->Interest->save();
				break;
			case 'netflix':
				list ($in_queue,$seen,$recos) = $this->getNetflixContent();
				$nf_data = array_merge($in_queue,$seen,$recos);	
				$your_genres = $this->figure_out_movie_genre(array(), $nf_data, false);
				if ($user['facebook_access_key']!='' ){
					$fb_user_movies=$this->grab_facebook_movie_data($fb_user_movies);
					$your_genres = $this->figure_out_movie_genre($your_genres,$fb_user_movies,true);
				}
				$this->Movie->create();
				$this->data['Movie']['body'] = json_encode($your_genres);
				$this->data['Movie']['user_id']=$this->Auth->getUserId();
				$this->Movie->set($this->data);
				$this->Movie->save();
			
				break;
		}
		
	}
	function store_master_data($master){
		$user = $this->Auth->getUserInfo();
		$name = preg_split('/[\f\n\r\t\v ]/',$master->name);
		$last_name = '';
		$first_name = '';
		for ($counter=0;$counter<sizeof($name);$counter++){
			if($counter==(sizeof($name)-1)) {
				$last_name = $name[$counter];
			}
			else {
				$first_name .= $name[$counter];
			}
		}
		
		$this->Userprofile->create();
		if (sizeof($name) == 1) $this->data['Userprofile']['first_name'] = $name[0];
		else {
			$this->data['Userprofile']['first_name']=ucwords($first_name);
			$this->data['Userprofile']['last_name'] = ucwords($last_name);
		}
		$this->data['Userprofile']['hometown']= $master->hometown;
		$this->data['Userprofile']['birthday']=date("Y-m-d H:i:s", strtotime($master->birthday));
		$this->data['Userprofile']['gender']=$master->gender;
		$this->data['Userprofile']['relationship']=$master->status;
		$this->data['Userprofile']['religion']=$master->religion;
		$this->data['Userprofile']['political']=$master->political;
		$this->data['Userprofile']['user_id']=$this->Auth->getUserId();
		$this->Userprofile->set($this->data);
		$this->Userprofile->save();
		$this->User->read(null,$this->Auth->getUserId());
		$this->data['User']['name']=$this->data['Userprofile']['first_name'].' '.$this->data['Userprofile']['last_name'];
		$this->User->set($this->data);
		$this->User->save();
		
	}
	
	function getMeetupData(){
		$user=$this->Auth->getUserInfo();
		$accessToken = $this->Session->read('meetup_access_token');
		$consumer_mu = $this->createConsumer('meetup');
		$getData = array('relation'=>'self',
						 'sess'=>'oauth_session');
		$interests=array();
		$db_results = $this->User->find('first', array('conditions' => (array('User.id'=>$this->Auth->getUserId()))));

		
	
		
		
		$content_mu = $consumer_mu->get($accessToken->key,$accessToken->secret,'https://api.meetup.com/members.json',$getData);
		$mu_user = json_decode($content_mu);
		if ($user['path']=='default.png'){
			$this->User->read(null,$this->Auth->getUserId());
			$this->data['User']['path']=$mu_user->results[0]->photo_url;
			$this->User->set($this->data);
			$this->User->save();
		
		}
		if (empty($db_results['Userprofile']['id'])){
			$this->Userprofile->create();
			$name = preg_split('/[\f\n\r\t\v ]/',$mu_user->results[0]->name);
			$last_name = '';
			$first_name = '';
			for ($counter=0;$counter<sizeof($name);$counter++){
				if($counter==(sizeof($name)-1)) {
					$last_name = $name[$counter];
				}
				else {
					$first_name .= $name[$counter];
				}
			}
			$this->data['Userprofile']['first_name'] = $first_name;
			$this->data['Userprofile']['last_name'] = $last_name;
			$this->data['Userprofile']['user_id']=$this->Auth->getUserId();
			$this->Userprofile->set($this->data);
			$this->Userprofile->save();	
				$this->User->read(null,$this->Auth->getUserId());
		$this->data['User']['name']=$this->data['Userprofile']['first_name'].' '.$this->data['Userprofile']['last_name'];
		$this->User->set($this->data);
		$this->User->save();
	
		}
	
		for($counter=0;$counter<sizeof($mu_user->results[0]->topics);$counter++){
			$interests[$counter]->description=$mu_user->results[0]->topics[$counter]->name;
		}
		return $interests;
	}
	
	
	function getFacebookData(){
		$user=$this->Auth->getUserInfo();
		$accessToken = $this->Session->read('facebook_access_key');
		$fb_user = json_decode(file_get_contents('https://graph.facebook.com/me?' . $accessToken));
		$user_info->name=$fb_user->name;
		$user_info->hometown=$fb_user->hometown->name;
		$user_info->location=$fb_user->location->name;
		$user_info->birthday=$fb_user->birthday;
	
				// get the lat long of this and compare to foursquare for travel information
				
		for ($counter=0;$counter<sizeof($fb_user->work);$counter++){
			$user_info->work[$counter]=$fb_user->work[$counter]->employer->name;
		}
			
				for ($counter=0;$counter<sizeof($fb_user->education);$counter++){
					$user_info->education[$counter]=$fb_user->education[$counter]->school->name;
				}
				$user_info->gender=$fb_user->gender;
				$user_info->status=$fb_user->relationship_status;
				$user_info->religion=$fb_user->religion;
				$user_info->political=$fb_user->political;
				
				//get likes 
				$fb_user_likes = json_decode(file_get_contents('https://graph.facebook.com/me/likes?'.$accessToken));
			
					$fb_user_movies = json_decode(file_get_contents('https://graph.facebook.com/me/movies?'.$accessToken));
				
				//get music
				$fb_user_music = json_decode(file_get_contents('https://graph.facebook.com/me/music?'.$accessToken));
				//$this->set('fb_user_music',$fb_user_music);
				
				//get books
				$fb_user_books = json_decode(file_get_contents('https://graph.facebook.com/me/books?'.$accessToken));
				//$this->set('fb_user_books',$fb_user_books);
				
				//get places
				$fb_user_checkins = json_decode(file_get_contents('https://graph.facebook.com/me/checkins?'.$accessToken));
				//$this->set('fb_user_checkins',$fb_user_checkins);
				
	//		var_dump($user_info);	
			return array($user_info, $fb_user_checkins, $fb_user_movies, $fb_user_likes);
	}
	function view_my_profile(){
		$user = $this->Auth->getUserInfo();
		var_dump($user);
		$user_info=array();
	
		if(is_null($this->Auth->getUserId())){
       		Controller::render('/deny');
        }
		else {
						
	
			$user = $this->Auth->getUserInfo();
			$db_results = $this->User->find('first', array('conditions' => (array('User.id'=>$this->Auth->getUserId()))));
			if (!empty($db_results)){
				$movie_data = (isset($db_results['Movie']['body'])) ? true : false;
			}
			//var_dump($db_results);
		
			//google
			/*
			if ($user['go_access_key']!=''){
				$consumer_go = $this->createConsumer('google');
				$getData_go = array('orderby'=>'starttime');
				$content_go=$consumer->get($accessToken->key,$accessToken->secret,'http://www.google.com/calendar/feeds/default/allcalendars/full?orderby=starttime.', $getData_go);
				$go_user = json_decode($content_go);
				$this->set('go_user',$go_user);
			}
			*/		//var_dump($db_results);
	
			
				$this->set('pic',$db_results['User']['path']);
			//pandora
//			$this->set(compact('user'));
			if (!empty($db_results['Movie']['body'])){
				$movies = json_decode($db_results['Movie']['body']);
				$top_movies = array();
				foreach ($movies as $key=>$value){
					array_push($top_movies,$key);
				}
				$this->set('top_movies',$top_movies);
			}
			if (!empty($db_results['Interest']['body'])){
				$interests = json_decode($db_results['Interest']['body']);
				$top_interests = array();
				foreach ($interests as $key=>$value){
					array_push($top_interests,$key);
				}
				$this->set('top_interests',$top_interests);
			}
			if (!empty($db_results['Place']['locations'])){
				$locations = json_decode($db_results['Place']['locations']);
				$top_locations = array();
				foreach ($locations as $key=>$value){
					array_push($top_locations,$key);
				}
				$this->set('top_locations',$top_locations);
			}
			if (!empty($db_results['Place']['categories'])){
				$categories = json_decode($db_results['Place']['categories']);
				$top_categories = array();
				var_dump($db_results['Place']['categories']);
				foreach ($categories as $key=>$value){
					array_push($top_categories,$key);
				}
				$this->set('top_categories',$top_categories);
			}
			if (!empty($db_results['Interest']['you_body'])){
				$you = json_decode($db_results['Interest']['you_body']);
				$top_you = array();
				foreach ($you as $key=>$value){
					array_push($top_you,$key);
				}
				$this->set('top_you',$top_you);
			}
			
			if (!empty($db_results['Work']['body'])){
				$work = json_decode($db_results['Work']['body']);
				
				//$this->set('work'
				
			}
			if (!empty($db_results['Work']['titles'])){
				$work = json_decode($db_results['Work']['titles']);
		
				foreach ($work as $key=>$value){
					$this->set('titles',$key);			
					break;
				}
			
			}
			if (!empty($db_results['Work']['industries'])){
				$work = json_decode($db_results['Work']['industries'],true);
				$first = true;
				arsort($work, SORT_NUMERIC);
				$top_industries = array();
				foreach ($work as $key=>$value){
					if ($first){
						$this->set('industries',$key);			
						$first = false;
					}
					array_push($top_industries, $key);
				}
				$this->set('top_industries',$top_industries);
				
			}
			if (!empty($db_results['School']['body'])){
				$school = json_decode($db_results['School']['body']);
				$parsable_array = array();
				$schools =array();
				$areas_of_focus = array();
				for ($counter = 0;$counter<sizeof($school);$counter++){
					foreach($school[$counter] as $key=>$value){
						if ($key == 'degree') $parsable_array[$key].=$value;
						if ($key == 'school') array_push($schools,$value);
						if ($key == 'major') array_push($areas_of_focus,$value); 
					}
				}
				$master_degree = false;
				$bach_degree = false;
				$doctor_degree = false;
				$doctor_array = array('/\b(?i)phd*\b/','/\bdoctor*\b/');
				$master_array = array('/\bMS\b/','/\b(?i)master*/');
				$bach_array = array('/\b(?i)bachelor*\b/','/\bBS\b/');
				preg_replace($doctor_array,'',$parsable_array['degree'],-1,$doctor);
				if ($doctor>0) $doctor_degree = true;
				preg_replace($master_array,'',$parsable_array['degree'],-1,$masters);
				if ($masters>0) $master_degree = true;
				preg_replace($bach_array,'',$parsable_array['degree'],-1,$bachelors);
				if ($bachelors>0) $bach_degree = true;
				$this->set('bach_degree',$bach_degree);
				$this->set('master_degree',$master_degree);
				$this->set('areas_of_focus',$areas_of_focus);
				$this->set('schools',$schools);
				
			}
		
			
			
	
			$this->set('user',$db_results);
	
	
			
		}
		
		// for travel look at 4sq for outside of the country
	}
	// movie_data not as much weight unless its facebook
	
	
	

	
	
	
	
}

?>