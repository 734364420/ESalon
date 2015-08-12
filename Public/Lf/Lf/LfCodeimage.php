<?php
class LfCodeimage {
	static function codeimage() {
		$image = imagecreatetruecolor(100, 30);
		$bgcolor = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $bgcolor);
		$code = '';
		for ($i = 0; $i < 4; $i++) {
			$fontsize = 6;
			$fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
			$data = "abcdefghjkmnpqrstuvwxy3456789";
			$fontcontent = substr($data, rand(1, strlen($data) - 1), 1);
			$code .= $fontcontent;
			$x = ($i * 100 / 4) + rand(5, 10);
			$y = rand(5, 10);
			imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
		}
		for ($i = 0; $i < 200; $i++) {
			$pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
			imagesetpixel($image, rand(1, 99), rand(1, 29), $pointcolor);
		}
		for ($i = 0; $i < 2; $i++) {
			$linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
			imageline($image, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $linecolor);
		}
		$return['code'] = $code;
		$return['image'] = $image;
		return $return;
//		header('content-type:image/png');
		//		imagepng($image);

	}
}