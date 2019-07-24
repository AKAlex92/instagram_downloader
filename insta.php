<?php
class Insta
{
	function __construct($url)
	{
		// $this->link = ($this->validate_url($url) === true ? $url : "");
		$this->link = $url;
		$this->is_video = false;
		$this->data = "";
		$this->title = "";
		$this->output = array(); // output = array('errors' [array], 'messages' [array], 'warnings' [array])
	}

    function __destruct() 
	{
		// print "Destroying " . __CLASS__ . "\n";
		// var_dump($output);
	}

	function start()
	{
		$this->validate_url($this->link);
		$html = file_get_contents($this->link);
		$this->is_video = $this->what_type_of_file($html);
		if($this->is_video)
		{
			$this->data = $this->get_video($html);
		}
		else
		{
			$this->data = $this->get_image($html);
		}
		$this->title = $this->get_caption($html);
		$this->header_download();
		// 
		return;
	}


	function get_string_between($string, $start, $end)
	{
		/*
		Example: 
		$fullstring = 'this is my [tag]dog[/tag]';
		$parsed = get_string_between($fullstring, '[tag]', '[/tag]');
		echo $parsed; // (result = dog)
		*/
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	function validate($str)
	{
		$max_chars = 50;
		$arr_replace = array(" " => "_", "." => "", "\n" => "");
		$output = $str;
		foreach ($arr_replace as $what => $with) 
		{
			$output = str_replace($what, $with, $output);
		}
		$output = substr($output, 0, $max_chars);
		return $output;
	}

	function validate_url($url)
	{
		$correct = true;
		$regex = '/(https?:\/\/(www\.)?)?instagram\.com(\/p\/\w+\/?)/';
		$check = preg_match($regex, $url, $matches);
		if($check == 0)
		{
			$correct = false;
			$this->create_error("Error: Enter a valid Instagram link");
		}
		return $correct;
	}

	function what_type_of_file($code)
	{
		$parsed = $this->get_string_between($code, '<script type="text/javascript">window._sharedData = ', ';</script>');
		$moreinfo = json_decode($parsed);
		$type = $moreinfo->entry_data->PostPage[0]->graphql->shortcode_media->is_video;
		return $type;
	}

	function get_image($code)
	{
		$parsed = $this->get_string_between($code, '<script type="text/javascript">window._sharedData = ', ';</script>');
		$moreinfo = json_decode($parsed);
		$vid = $moreinfo->entry_data->PostPage[0]->graphql->shortcode_media->display_url;
		$raw_vid = file_get_contents($vid);
		return $raw_vid;
	}

	function get_video($code)
	{
		$parsed = $this->get_string_between($code, '<script type="text/javascript">window._sharedData = ', ';</script>');
		$moreinfo = json_decode($parsed);
		$img = $moreinfo->entry_data->PostPage[0]->graphql->shortcode_media->video_url;
		$raw_img = file_get_contents($img);
		return $raw_img;
	}

	function get_caption($code)
	{
		$parsed = $this->get_string_between($code, '<script type="application/ld+json">', '</script>');
		$info = json_decode($parsed);
		$title = $this->validate($info->caption);
		return $title;
	}

	function header_download($mime = "image/jpeg")
	{
		if($this->data != "" && count($this->output['errors']) == 0)
		{
			$ext = "jpg";
			if($this->is_video)
			{
				$mime = "video/mp4";
				$ext = "mp4";
			}
			if($this->title == "")
			{
				$this->title = time();
			}
			// TO IMPLEMENT AFTER: https://www.php.net/manual/en/function.image-type-to-mime-type.php
			// Set the content type header - in this case image/jpeg;
			header('Content-Type: ' . $mime);
			// It will be called title.jpg
			header('Content-Disposition: attachment; filename="'.$this->title.'.' . $ext .'"');
			echo $this->data;
			return;
		}
	}

	function create_error($msg, $error_code = 0)
	{
		$this->output['errors'][] = $msg;
	}

	function get_errors_info_warnings($flag_json = 0)
	{
		if($flag_json == 0)
		{
			return $this->output;
		}
		else
		{
			return json_encode($this->output);
		}
		
	}
}
?>