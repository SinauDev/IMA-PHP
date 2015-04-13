<?php

// CLI Version of Zebra_Image by aancw
// https://github.com/aancw/IMA-PHP
// This file need Zebra_Image library
// Thanks to Zebra_Image. Github : https://github.com/stefangabos/Zebra_Image/

require dirname(__FILE__).'/Zebra_Image/Zebra_Image.php';

$argument1 = "";
if( isset($argv[1]) )
	$argument1 = $argv[1];

function CheckArgumentIsEmpty( $argument )
{
	if( empty($argument) )
		return true;
	else
		return false;
}

function ShowHelp( )
{
	echo "Run like this \"php IMP.php -f <Full Filename Path> -m <method>\"\n";
	echo "Method :\n";
	echo "1. Cropping Image\n";
	echo "2. Resize Image\n";
	echo "3. Rotate Image\n";
	echo "4. Flipped Vertically\n";
	echo "5. Flipped Horizontally\n";
	echo "6. Sepia Filter\n";
	
	echo "Choose what you want : ";
	
	$handle = fopen ("php://stdin","r");
	$line = fgets($handle);
	switch( trim($line) )
	{
		case 1:
			echo "Run php IMA.php -f <Full Filename Path> -m 1 <start_x>,<start_y>,<end_x>,<end_y>\n";
			echo "start_x\tx coordinate to start cropping from\n";
			echo "start_y\ty coordinate to start cropping from\n";
			echo "end_x\tx coordinate where to end the cropping\n";
			echo "end_y\ty coordinate where to end the cropping\n";
			break;
		case 2:
			echo "Run php IMA.php -f <Full Filename Path> -m 2 <width>,<height>\n";
			echo "Width\tWidth base on pixel\n";
			echo "Height\tHeight base on pixel\n";
			break;
		case 3:
			echo "Run php IMA.php -f <Full Filename Path> -m 3 <DegreesValue>\n";
			break;
		case 4:
			echo "Run php IMA.php -f <Full Filename Path> -m 4 \n";
			break;
		case 5:
			echo "Run php IMA.php -f <Full Filename Path> -m 5 \n";
			break;
		case 6:
			echo "Run php IMA.php -f <Full Filename Path> -m 6 \n";
			break;
		default:
			echo "Nothing to do here";
			break;
	}
	exit;
}

function show_error($error_code, $source_path, $target_path)
{

	// if there was an error, let's see what the error is about
	switch ($error_code) {

		case 1:
			echo 'Source file "' . $source_path . '" could not be found!';
			break;
		case 2:
			echo 'Source file "' . $source_path . '" is not readable!';
			break;
		case 3:
			echo 'Could not write target file "' . $source_path . '"!';
			break;
		case 4:
			echo $source_path . '" is an unsupported source file format!';
			break;
		case 5:
			echo $target_path . '" is an unsupported target file format!';
			break;
		case 6:
			echo 'GD library version does not support target file format!';
			break;
		case 7:
			echo 'GD library is not installed!';
			break;
		case 8:
			echo '"chmod" command is disabled via configuration!';
			break;

	}

}

function ImageProcessing($filenamePath, $method, $option)
{
	$image = new Zebra_Image();
	
    // indicate a source image
    $image->source_path = $filenamePath;

    $ext = substr($image->source_path, strrpos($image->source_path, '.') + 1);
	
	$filename = basename( $filenamePath, ".".$ext);
	$methodText = "";
	$methodInfo = "";
	$width = 0;
	$height = 0;
	$sy = 0;
	$sx = 0;
	$ey = 0;
	$ex = 0;
	$degrees = 0;
	
	switch( $method )
	{
		case 1:
			$methodText = "crop";
			$methodInfo = "Cropping";
			break;
		case 2:
			$methodText = "resize";
			$methodInfo = "Cesizing";
			break;
		case 3:
			$methodText = "rotate";
			$methodInfo = "Rotating";
			break;
		case 4:
			$methodText = "flip-v";
			$methodInfo = "Flip vertically";
			break;
		case 5:
			$methodText = "flip-h";
			$methodInfo = "Flip horizontally";
			break;
		case 6:
			$methodText = "sepia";
			$methodInfo = "Sepia filter";
			break;
		default:
			echo "Nothing to do here";exit;
			break;
			
	}
	echo "IMA-PHP by aancw\n";
	echo "Processing image " . $image->source_path ."\n";
	
	$image->target_path = 'results/'.$filename. "-" .$methodText. "." . $ext;
	
	$exp = explode(',',$option) ;
	
	if( $methodText === "crop" )
	{
		if( isset($exp[0]) && isset($exp[1]) && isset($exp[2]) && isset($exp[3]) )
		{
			$sx = $exp[0];
			$sy = $exp[1];
			$ey = $exp[2];
			$ex = $exp[3];
		}
		
		if (!$image->crop($sx, $sy, $ey, $ex)) show_error($image->error, $image->source_path, $image->target_path);
		
	}
	elseif( $methodText === "resize" )
	{
		if( isset($exp[0]) && isset($exp[1]) )
		{
			$width = $exp[0];
			$height = $exp[1];
		}
		
		if (!$image->resize($width, $height, ZEBRA_IMAGE_BOXED, -1)) show_error($image->error, $image->source_path, $image->target_path);
		
	}
	elseif( $methodText === "rotate" )
	{
		if( isset($exp[0]) )
		{
			$degrees = $exp[0];
			if (!$image->rotate($degrees)) show_error($image->error, $image->source_path, $image->target_path);
		}
	}elseif( $methodText === "flip-v" )
	{
		if (!$image->flip_vertical()) show_error($image->error, $image->source_path, $image->target_path);
		
	}elseif( $methodText === "flip-h" )
	{
		if (!$image->flip_horizontal()) show_error($image->error, $image->source_path, $image->target_path);
		
	}elseif( $methodText === "sepia" )
	{
		$image->apply_filter(array(
        array('grayscale'),
        array('colorize', 90, 60, 40),
		));
	}
	
	echo $methodInfo . " image is done and result has been saved to " .dirname(__FILE__)."/".$image->target_path;
}

if (!is_dir('results') || !is_writable('results'))
{

        echo "Please create the results folder at ".dirname(__FILE__)." and make sure it is writable!\n";
		exit;
}else
{
	if( CheckArgumentIsEmpty( $argument1 ) )
		ShowHelp();	
	else
	{
		$filePath	= "";
		$method		= 0;
		$optionValue = "";
			
		for($i = 0;$i < 6;$i++)
		{
			if( isset( $argv[$i] ) )
			{
				if( $argv[$i] === "-f"  )
				{
					if( isset($argv[$i+1]) )
						$filePath = $argv[$i+1];
				}
				if( $argv[$i] === "-m"  )
				{
					if( isset($argv[$i+1]) && isset($argv[$i+2]) )
						if( is_numeric( $argv[$i+1] ) )
						{
							$method = $argv[$i+1];
							$optionValue = $argv[$i+2];
						}
						
				}
				if( $argv[$i] === "-h" )
					ShowHelp();
			}
		}
		
		ImageProcessing($filePath, $method, $optionValue);
	}	
}
?>