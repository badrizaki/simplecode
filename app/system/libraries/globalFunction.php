<?php namespace system\libraries;

class globalFunction
{
	function __construct()
	{
	}

	public function redirect($url = '')
	{
		header('Location: ' . $url);die;
	}

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    public function toArray($d)
    {
        return json_decode(json_encode($d), True);
    }

    # Convert object data to array
    public function objectToArray($d)
    {
        if (is_object($d))
        {
            $d = get_object_vars($d);
        }
        
        if (is_array($d))
        {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }

    # Function for send email with SMTP
    public function sendEmail($param = array())
    {
        // set_time_limit(0);
        if ($param['config']['mode'] == "customMail")
        {
            $subject    = $param['title'];
            $message    = $param['body'];
            $to         = $param['to'];

            $headers    = "MIME-Version: 1.0" . "\r\n";
            $headers   .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

            // More headers
            $headers .= 'From: <'.$param['from'].'>' . "\r\n";
            $headers .= 'Cc: ' . $param['from'] . "\r\n";
            @mail($to,$subject,$message,$headers);
            if(@mail)
            {
                return "Email sent successfully !!";
            }

        } else {
            $from     = $param['from'];
            $fromName = $param['fromName'];
            $email    = $param['to'];
            $title    = $param['title'];
            $body     = $param['body'];
            $altBody  = $param['altBody'];

            /*require_once "PHPMailer/PHPMailerAutoload.php";*/
            require_once "PHPMailer/class.phpmailer.php";
            require_once "PHPMailer/class.smtp.php";
            $mail = new PHPMailer;

            # Check mode
            if ($param['config']['mode'] == "mail")
            {
                $mail->IsMail();
            } else {
                $mail->IsSMTP();
                // $mail->Mailer   = "smtp";

                ## Enable SMTP debugging
                ## 0 = off (for production use)
                ## 1 = client messages
                ## 2 = client and server messages
                $mail->SMTPDebug = 0;

                ## Ask for HTML-friendly debug output
                $mail->Debugoutput = 'html';

                ## Set the hostname of the mail server
                $mail->Host       = $param['config']['emailHost'];
                // $mail->Host       = "smtp.gmail.com";  # sets GMAIL as the SMTP server

                ## Set the SMTP port number - likely to be 25, 465 or 587
                $mail->Port       = $param['config']['emailPort'];
                // $mail->Port       = "465";  # set GMAIL the SMTP port
                
                ## Whether to use SMTP authentication
                $mail->SMTPAuth   = true;

                ## Username to use for SMTP authentication
                $mail->Username   = $param['config']['user'];       # username

                ## Password to use for SMTP authentication
                $mail->Password   = $param['config']['password'];   # password

                # sets the prefix to the server
                $mail->SMTPSecure = $param['config']['smtpSecure']; 
            }

            try
            {
                // $mail->From       = $from;
                // $mail->FromName   = $fromName;

                ## Set who the message is to be sent from
                $mail->SetFrom($from, $fromName);
                
                ## Set an alternative reply-to address
                // $mail->addReplyTo('replyto@example.com', 'First Last');

                ## Set who the message is to be sent to
                $mail->addAddress($email, $fromName);
                
                ## Set the subject line
                $mail->Subject    = str_replace("\\",'',$title);

                ## Read an HTML message body from an external file, convert referenced images to embedded,
                ## convert HTML into a basic plain-text alternative body
                $mail->MsgHTML(str_replace("\\",'',$body));
                // $mail->msgHTML(str_replace("\\",'',$body));
                $mail->Body       = str_replace("\\",'',$body);

                $mail->AltBody    = $altBody; # Text Body
                // $mail->AltBody = 'The body message in plain text';

                // $mail->Timeout = 3600;
                $mail->Priority = 1;
                $mail->AddCustomHeader("X-MSMail-Priority: High");
                $mail->WordWrap = 50;

                ## send as HTML
                // $mail->IsHTML(true); 
                
                if($mail->Send())
                {
                    // return true;
                    return $mail;
                } else {
                    return $mail->ErrorInfo;;
                }
                $mail->ClearAddresses();
            }catch(Exception $e){
                $err =  $e;
                return $err;
            }
        }
    }

	# Create image from base64
	public function createImageFromBase64($base64, $maxSize=200)
	{
	    $img = imagecreatefromstring(base64_decode($base64));
	    if (!$img) return false;

	    $destFile = tempnam("/tmp","ams");
	    imagepng($img, $destFile);
	    $info = getimagesize($destFile);
	    
	    if ($info[0] > 0 && $info[1] > 0 && $info['mime'])
	    {
	        $width_ori = $info[0];
	        $height_ori = $info[1];
	        $type = $info['mime'];
	        
	        $ratio_ori = $width_ori / $height_ori;

	        $width  = $maxSize; 
	        $height = $maxSize;

	        # resize to height (portrait) 
	        if ($ratio_ori < 1)
	            $width = $height * $ratio_ori;
	        # resize to width (landscape)
	        else
	            $height = $width / $ratio_ori;
	        
	        ini_set('memory_limit', '512M'); 
	        
	        $image = imagecreatefrompng($destFile);
	        # create a new blank image
	        $newImage = imagecreatetruecolor($width, $height);

	        # Copy the old image to the new image
	        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width_ori, $height_ori);
	        imagepng($newImage, $destFile);

	        imagedestroy($newImage);

	        if (is_file($destFile))
	        {
	            $data = file_get_contents($destFile);

	            # Remove the tempfile
	            unlink($destFile);
	            return $data;
	        }
	    }
	    return false;
	}

	## Upload image with move_uploaded_file
    public function uploadImage($dir = "", $dir_default = 'uploads/', $files = array(), $post = array(), $filename = "")
    {
        ## full directory
        $target_dir = $dir . $dir_default;

        ## New filename
        $temp = explode(".", $files["imageUpload"]["name"]);
        if ($filename == "") $filename = round(microtime(true));
        $newfilename = $filename . '.' . end($temp);    
        $target_file = $target_dir . $newfilename;
        // $target_file = $target_dir . basename($files["imageUpload"]["name"]);
        
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
        ## Create directory
        if (!is_dir($target_dir)) mkdir($target_dir,0777,true);

        if(isset($post["submit"]))
        {
            $check = getimagesize($files["imageUpload"]["tmp_name"]);
            if($check !== false) 
            {
                // echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                // echo "File is not an image.";
                return array(false, "File is not an image.");
                $uploadOk = 0;
            }
        }

        if (file_exists($target_file)) 
        {
            // echo "Sorry, file already exists.";
            return array(false, "Sorry, file already exists.");
            $uploadOk = 0;
        }

        if ($files["imageUpload"]["size"] > 5000000) 
        {
            // echo "Sorry, your file is too large.";
            return array(false, "Sorry, your file is too large.");
            $uploadOk = 0;
        }

        if($imageFileType != "jpg"
            && $imageFileType != "png"
            && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            return array(false, "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            $uploadOk = 0;
        }

        if ($uploadOk == 0)
        {
            // echo "Sorry, your file was not uploaded.";
            return array(false, "Sorry, your file was not uploaded.");
        } else {
            // move_uploaded_file($_FILES["file"]["tmp_name"], "../img/imageDirectory/" . $newfilename);
            if (move_uploaded_file($files["imageUpload"]["tmp_name"], $target_file))
            {
                // echo "The file ". basename($files["imageUpload"]["name"]). " has been uploaded.";
                return array(true, $dir_default . $newfilename);
                // return array(true, $dir_default . basename($files["imageUpload"]["name"]));
            } else {
                // echo "Sorry, there was an error uploading your file.";
                return array(false, "Sorry, there was an error uploading your file.");
            }
        }
    }

    ## Upload file 
    public function uploadFile($dir = "", $dir_default = 'uploads/', $files = array(), $filename = "", $overwrite = false)
    {
        ## full directory fileUpload
        $target_dir = $dir . $dir_default;

        ## New filename
        $temp = explode(".", $files["name"]);
        if ($filename == "") $filename = round(microtime(true));
        $newfilename = $filename . '.' . end($temp);    
        $target_file = $target_dir . $newfilename;
        // $target_file = $target_dir . basename($files["name"]);
        
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
        ## Create directory
        if (!is_dir($target_dir)) mkdir($target_dir,0777,true);

        if (file_exists($target_file)) 
        {
            if ($overwrite) {
                unlink($target_file);
            } else {
                return array(false, "Sorry, file already exists.");
                $uploadOk = 0;
            }
        }

        if ($files["size"] > 5000000) 
        {
            return array(false, "Sorry, your file is too large.");
            $uploadOk = 0;
        }

        if(strtolower($imageFileType) != "doc"
            && strtolower($imageFileType) != "docx"
            && strtolower($imageFileType) != "pdf"
        ) {
            return array(false, "Sorry, only DOC, DOCX, & PDF files are allowed.");
            $uploadOk = 0;
        }

        if ($uploadOk == 0)
        {
            return array(false, "Sorry, your file was not uploaded.");
        } else {
            if (move_uploaded_file($files["tmp_name"], $target_file))
            {
                return array(true, $dir_default . $newfilename);
            } else {
                return array(false, "Sorry, there was an error uploading your file.");
            }
        }
    }

    ## Change format array for image
    public function reArrayFiles($file)
    {
        $file_ary = array();
        $file_count = count($file['name']);
        $file_key = array_keys($file);
       
        for($i=0;$i<$file_count;$i++)
        {
            foreach($file_key as $val)
            {
                $file_ary[$i][$val] = $file[$val][$i];
            }
        }
        return $file_ary;
    }

    # Change date format to format date Indonesia
    public function formatDate($date)
    {
        $result = $date;
        if (strtotime($date) > 0)
        {
            # Month in indonesia
            $bulanText = array("Januari", "Februari", "Maret",
                               "April", "Mei", "Juni",
                               "Juli", "Agustus", "September",
                               "Oktober", "November", "Desember");

            # Get year
            $tahun = substr($date, 0, 4);
            # Get Month
            $bulan = substr($date, 5, 2);
            # Get Date
            $tgl   = substr($date, 8, 2);

            # Convert format date to (9 Agustus 2015)
            $result = $tgl . " " . $bulanText[(int)$bulan-1] . " ". $tahun;
        }
        return $result;
    }

    ## get paragraph with maximal words
    public function maxWords($content = '', $maxLength = 100, $endWords = ' ... ')
    {
        $words = explode(' ', $content);
        if (count($words) > $maxLength)
        {
            $pos = strpos($content, ' ', $maxLength);
            $content = substr($content, 0, $pos).$endWords;
        }
        return $content;
    }

	# Replace space and any char with (-)
	public function replaceUrl($url = '')
	{
	    $url = str_replace(" ", "-", $url);
	    $url = str_replace("---------", "-", $url);
	    $url = str_replace("--------", "-", $url);
	    $url = str_replace("-------", "-", $url);
	    $url = str_replace("------", "-", $url);
	    $url = str_replace("-----", "-", $url);
	    $url = str_replace("----", "-", $url);
	    $url = str_replace("---", "-", $url);
	    $url = str_replace("--", "-", $url);
	    $url = str_replace("?", "", $url);

	    return $url;
	}

	## Times ago
	public function timeElapsedString($ptime = '')
	{
	    $etime = time() - strtotime($ptime);

	    if ($etime < 1)
	    {
	        return '1 seconds';
	    }

	    $a = array( 365 * 24 * 60 * 60  =>  'year',
	                 30 * 24 * 60 * 60  =>  'month',
	                      24 * 60 * 60  =>  'day',
	                           60 * 60  =>  'hour',
	                                60  =>  'minute',
	                                 1  =>  'second'
	    );

	    $a_plural = array( 'year'   => 'years',
	                       'month'  => 'months',
	                       'day'    => 'days',
	                       'hour'   => 'hours',
	                       'minute' => 'minutes',
	                       'second' => 'seconds'
	    );

	    foreach ($a as $secs => $str)
	    {
	        $d = $etime / $secs;
	        if ($d >= 1)
	        {
	            $r = round($d);
	            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
	        }
	    }
	}
}