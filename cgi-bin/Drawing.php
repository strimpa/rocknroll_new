<?php

class Drawing
{
	//header("Content-Type: image/png");
	
	private static $rootPath = "images/temp/";

	static function RandRect($num)
	{
		$filename = Drawing::$rootPath ."RandRect_$num.png";
	
		$dim = array(200, 35);

		$im = @imagecreatetruecolor($dim[0], $dim[1])
			  or die('Cannot Initialize new GD image stream');
		imagesavealpha($im, true);
		$bg_color = imagecolorallocatealpha($im, 0, 0, 0, 127);
		$fg_color = imagecolorallocate($im, 0x07, 0x5A, 0xAC);
		$line_color = imagecolorallocate($im, 0,0,0);
		//$text_color = imagecolorallocate($im, 233, 14, 91);
		//imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);

		$offset = 8;
		$corners = array($offset,$offset,$offset,$dim[1]-$offset,$dim[0]-$offset,$dim[1]-$offset,$dim[0]-$offset,$offset);
		$lineCornerOffset = 3;
		$lineCorners = array(-$lineCornerOffset,-$lineCornerOffset,-$lineCornerOffset,$lineCornerOffset,$lineCornerOffset,$lineCornerOffset,$lineCornerOffset,-$lineCornerOffset);
		$lineCornerArrayOffset = rand(0,3)*2;
		$randCorners = array();

		for($i=0;$i<count($corners);$i++)
		{
			array_push($randCorners, $corners[$i]+rand(-5,5));
		}

		$randLines = array();
		for($k=0;$k<3;$k++)
		{
			if($lineCornerArrayOffset>=count($randCorners))
				$lineCornerArrayOffset = 0;
			print "$lineCornerArrayOffset:$lineCornerArrayOffset";
			array_push($randLines, $randCorners[$lineCornerArrayOffset  ]+$lineCorners[$lineCornerArrayOffset  ]);
			array_push($randLines, $randCorners[$lineCornerArrayOffset+1]+$lineCorners[$lineCornerArrayOffset+1]);
			$lineCornerArrayOffset+=2;
		}
		
		print "verts: $randLines[0],$randLines[1] | $randLines[2],$randLines[3] | $randLines[4],$randLines[5]";

		// fill the background
		imagefill($im, 0, 0, $bg_color);

		// draw a polygon
		imagefilledpolygon($im, $randCorners, 4, $fg_color);
		imagesetthickness($im, 2);
		imageline($im, $randLines[0],$randLines[1],$randLines[2],$randLines[3], $fg_color);
		imageline($im, $randLines[2],$randLines[3],$randLines[4],$randLines[5], $fg_color);

		imagepng($im, $filename);
		imagedestroy($im);
		
		return "/".$filename;
	}
	
}
?>