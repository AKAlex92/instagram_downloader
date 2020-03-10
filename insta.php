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
		$this->filename = "";
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
		// $this->header_download();
		// $this->header_download_specific();
		$this->header_download_jquery();
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

	function header_download_jquery($mime = "image/jpeg")
	{
		if($this->data != "" && count($this->output['errors']) == 0)
		{
			$output_json = array();
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
			$this->filename = $this->title.'.' . $ext;
			$output_json['data'] = base64_encode($this->data);
			$output_json['filename'] = $this->filename;
			$output_json['mime'] = $mime;
			$output_json['title'] = $this->title;
			echo json_encode($output_json);
			return;
		}
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
			$this->filename = $this->title.'.' . $ext;
			// TO IMPLEMENT AFTER: https://www.php.net/manual/en/function.image-type-to-mime-type.php
			// Set the content type header - in this case image/jpeg;
			header('Content-Type: ' . $mime);
			// header("Cache-Control: no-store, no-cache");  
			// It will be called title.jpg
			header('Content-Disposition: attachment; filename="'. $this->filename .'"');
			echo $this->data;
			return;
		}
	}

	function header_download_specific($mime = "image/jpeg")
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
			$this->filename = $this->title.'.' . $ext;
			// TO IMPLEMENT AFTER: https://www.php.net/manual/en/function.image-type-to-mime-type.php
			// Set the content type header - in this case image/jpeg;
			if(!$this->isIphone() && false)
			{
				header('Content-Type: ' . $mime);
				// header("Cache-Control: no-store, no-cache");  
				// It will be called title.jpg
				header('Content-Disposition: attachment; filename="'. $this->filename .'"');
				echo $this->data;
			}
			else
			{
			?>
			<a id="link" href="#0">No download yet</a>
			<script  src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
			<script>
			$("#link")
				.attr("href", "data:application/octet-stream;base64," + encodeURIComponent('<?php echo base64_encode($this->data); ?>'))
				.attr("download", "<?php echo $this->filename; ?>")
				.attr("target", "_blank")
				.text("Download <?php echo $this->filename; ?>");
			/*
			fetch('<?php echo $this->file; ?>')
			.then(resp => resp.blob())
			.then(blob => {
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.style.display = 'none';
				a.href = url;
				// the filename you want
				a.download = '<?php echo $this->file; ?>';
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				alert('your file has downloaded!'); // or you know, something with better UX...
			})
			.catch(() => alert('oh no!'));
			*/
			/*
				var blob;
				blob = new Blob(['<?php echo base64_encode($this->data); ?>'], { type: "application/octet-stream" });

				$(document).ready(function() {
					$("#link")
						.attr("href", URL.createObjectURL(blob))
						.attr("download", "<?php echo $file; ?>")
						.attr("target", "_blank")
						.text("Download <?php echo $file; ?>"); 
				});
				*/
			</script>
			<?php
				/*
				header('Content-Disposition: attachment; filename="'.basename($file).'"' );
				header("Content-Length: " . filesize($file));
				header("Content-Type: application/octet-stream;");
				// header("Location: " . $file);
				// $file = "photo.jpg";
				// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				// header("Cache-Control: post-check=0, pre-check=0", false);
				// header("Pragma: no-cache");
				// header("Content-Description:  File Transfer");
				// header("Content-type:  " . $mime);
				// header("Content-Disposition:  attachment; filename=\"".$file."\"");
				// header("Content-Transfer-Encoding:  binary");
				// header("Content-Length:  ".filesize($file));
				// header("Upgrade-Insecure-Requests:  1");
				// ob_end_flush();
				@readfile($file);
				*/
				exit;
			}
			die();
			return;
		}
	}

	function isIphone()
	{
		$isApple = false;
		//Detect special conditions devices
		$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
		$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
		$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

		if($iPhone || $iPad || $iPod)
		{
			$isApple = true;
		}
		return $isApple; 
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