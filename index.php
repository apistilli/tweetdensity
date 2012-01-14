<?php

$instance = new tweetdensity;

# get and validate the input parameters
if($instance->validate()){
	# perform the call to twitter
	$response = $instance->call_twitter();
	# parse the response and count the tweets per hour
	$parsed = $instance->parse_response($response);
}
# write and send the output
print_r($instance->density);
print $instance->send_response();


class tweetdensity {
	var $type = '';
	var $handle = '';
	var $count = '';
	var $err;
	var $density = array();
	var $tweet_url = 'https://api.twitter.com/1/statuses/user_timeline';
	function validate(){
		$this->type = $_GET['type'];
		$this->handle = $_GET['handle'];
		$this->count = $_GET['count'];
		#checking validity of input and setting defaults, if necessary
		if($this->type != 'html' and $this->type != 'xml' and $this->type != 'json') $this->type = 'json';
		if(!is_numeric($this->count)) $this->count = 50;
		if(!$this->handle) {
			$this->err = 'handle is missing';
			return false;
		}
		return true;
	}
	function parse_response($res){
		print "create response";
		$obj = json_decode($res);
		foreach ($obj as $tw){
			$d = $tw->created_at;
			$t = strtotime($d);
			$h = date("H",$t);
			$this->density[$h]++;
			print $tw->text . " $d - $t - ". date("H",$t) . "<br />";
		}
		#print_r($obj);
	}

	function call_twitter(){
		$url = $this->tweet_url.'.json?screen_name='.$this->handle.'&count='.$this->count;
		print "tweet url: $url\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		$res = curl_exec($ch);
		if($res === false){
			echo "Curl error $url : ".  curl_error($ch). curl_errno($ch);
		}
		curl_close($ch);
		return $res;
	}

	function send_response(){
	}
}
