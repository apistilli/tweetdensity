<?php

$instance = new tweetdensity;

# get and validate the input parameters
if($instance->validate()){
	# perform the call to twitter
	$response = $instance->call_twitter();
	print $response;
	print "empty?";
	# parse the response and count the tweets per hour

}
# write and send the output
print $instance->create_response();


class tweetdensity {
	var $type = '';
	var $handle = '';
	var $count = '';
	var $err;
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
	function create_response(){
	}

	function call_twitter(){
		$url = $this->tweet_url.'.'.$this->type.'?screen_name='.$this->handle.'&count='.$this->count;
		print "tweet url: $url\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

	function parse_response(){
	}
}
