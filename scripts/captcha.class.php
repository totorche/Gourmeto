<?php

class captcha {

	private $randString = '';
	private $stringLength = 10;
	private $imageWidth = false;
	private $imageHeight = false;
	private $background = true;
	private $backgroundColor = array('R'=>255, 'V'=>255, 'B'=>255);
	private $borderColor = array('R'=>226, 'V'=>113, 'B'=>59);
	private $borderWidth = 0;
	private $textColor = array('R'=>0, 'V'=>0, 'B'=>0);
	private $forbiddenChars= array(1,0,'l','0');
	private $font = '';
	private $fontSize = 15;
	private $fromBorder = 10;
	private $type = '';
	private $shadow = false;
	private $shadowColor = array('R'=>128, 'V'=>128, 'B'=>128);
	private $shadowX = 2;
	private $shadowY = 2;
	private $backgroundImage = false;
	private $textAngle = 0;
	private $roundedCorners = false;
	private $roundedCornersRadius = 5;

	/**
	 * Constructeur - fixe le type d'image : PNG,GIF,JPEG
	 *
	 * @param string $type type de l'image
	 */
	public function __construct($type='PNG') {
		$this->setImageType($type);
	}

	/**
	 * Fixe la longueur de la cha�ne al�atoire g�n�r�e
	 *
	 * @param int $lenght longueur de la cha�ne
	 */
	public function setStringLenght($lenght) {
		$this->stringLength = $lenght;
	}

	/**
	 * Fixe la couleur de fond de l'image
	 *
	 * @param int $R rouge
	 * @param int $V vert
	 * @param int $B bleu
	 */
	public function setBackgroundColor($R,$V,$B) {
		$this->backgroundColor['R'] = $R;
		$this->backgroundColor['V'] = $V;
		$this->backgroundColor['B'] = $B;
	}

	/**
	 * Fixe la couleur de la bordure
	 *
	 * @param int $R rouge
	 * @param int $V vert
	 * @param int $B bleu
	 */
	public function setBorderColor($R,$V,$B) {
		$this->borderColor['R'] = $R;
		$this->borderColor['V'] = $V;
		$this->borderColor['B'] = $B;
	}

	/**
	 * Fixe la taille de la bordure
	 *
	 * @param int $width taille en pixel de la bordure
	 */
	public function setBorderWidth($width) {
		$this->borderWidth = (int)$width;
	}

	/**
	 * Fixe la couleur du texte
	 *
	 * @param int $R rouge
	 * @param int $V vert
	 * @param int $B bleu
	 */
	public function setTextColor($R,$V,$B) {
		$this->textColor['R'] = $R;
		$this->textColor['V'] = $V;
		$this->textColor['B'] = $B;
	}

	/**
	 * Fixe la largeur de l'image
	 *
	 * @param int $width largeur en pixel de l'image
	 */
	public function setImageWidth($width) {
		$this->imageWidth = $width;
	}

	/**
	 * Fixe la hauteur de l'image
	 *
	 * @param int $height hauteur en pixel de l'image
	 */
	public function setImageHeight($height) {
		$this->imageHeight = $height;
	}

	/**
	 * Fixe la police True Type et sa taille
	 *
	 * @param string $font chemin vers la police
	 * @param int $size taille de la police
	 */
	public function setFont($font, $size) {
		if(!is_readable($font)) {
			throw new Exception('La police est introuvable');
		}
		$this->font = $font;
		$this->fontSize = $size;
	}

	/**
	 * Fixe si une ombre doit �tre appliqu�e au texte
	 *
	 * @param int $x d�calage de l'ombre en absisse
	 * @param int $y d�calage de l'ombre en ordonn�
	 */
	public function setShadow($x=false,$y=false) {
		$this->shadow = true;
		if($x) {
			$this->shadowX = (int)$x;
		}
		if($y) {
			$this->shadowY = (int)$y;
		}
	}

	/**
	 * Fixe la couleur de l'ombre
	 *
	 * @param int $R rouge
	 * @param int $V vert
	 * @param int $B bleu
	 */
	public function setShadowColor($R,$V,$B) {
		$this->shadow = true;
		$this->shadowColor['R'] = $R;
		$this->shadowColor['V'] = $V;
		$this->shadowColor['B'] = $B;
	}


	/**
	 * D�finie si une image de fond doit �tre appliqu�e
	 *
	 * @param string $image chemin vers l'image
	 */
	public function setBackgroundImage($image) {
		if(!is_readable($image)) {
			throw new Exception('Image de fond introuvable');
		}
		$this->backgroundImage = $image;
	}

	/**
	 * Fixe l'angle du texte
	 *
	 * @param int $angle angle en degr�s
	 */
	public function setTextAngle($angle) {
		$this->textAngle = (int)$angle;
	}

	/**
	 * Fixe la taille de la marge par rapport � la bordure
	 *
	 * @param int $margin taille en pixel de la marge
	 */
	public function setMarginFromBorder($margin) {
		$this->fromBorder = (int)$margin;
	}
	
	public function setRoundedCorners($radius=false) {
		$this->roundedCorners = true;
		if($radius) {
			$this->roundedCornersRadius = (int)$radius;
		}
	}


	/**
	 * Construit l'image
	 *
	 */
	public function getImage() {
		if(!$this->font) {
			throw new Exception('Il faut charger une police');
		}

		$text = $this->getRandString();
		$text = trim(preg_replace('`(\w)`', '$1  ', $text));
		$box = imagettfbbox($this->fontSize,$this->textAngle,$this->font,$text);

		if(!$this->imageHeight) {
			$boxHeight = max($box[1],$box[3]) - min($box[7],$box[5]);
			$this->imageHeight = $boxHeight + $this->borderWidth*2 + $this->fromBorder*2;
		}
		if(!$this->imageWidth) {
			$boxWidth = max($box[4],$box[2]) - min($box[6],$box[0]);
			$this->imageWidth =  $boxWidth + $this->borderWidth*2 + $this->fromBorder*2;
		}

		if(function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
		} else {
			$im = imagecreate($this->imageWidth, $this->imageHeight);
		}
		// border
		if($this->borderWidth > 0) {
			$border = imagecolorallocate(
			$im,
			$this->borderColor['R'],
			$this->borderColor['V'],
			$this->borderColor['B']
			);
			if(!$this->roundedCorners) {
				imagefilledrectangle(
				$im,
				0,
				0,
				$this->imageWidth,
				$this->imageHeight,
				$border
				);
			} else {
				$this->ImageRectangleWithRoundedCorners(
				$im,
				0,
				0,
				$this->imageWidth,
				$this->imageHeight,
				$border,
				$this->roundedCornersRadius
				);
			}
		}

		// background
		$background = imagecolorallocate(
		$im,
		$this->backgroundColor['R'],
		$this->backgroundColor['V'],
		$this->backgroundColor['B']
		);
		imagefilledrectangle(
		$im,
		$this->borderWidth,
		$this->borderWidth,
		$this->imageWidth-$this->borderWidth,
		$this->imageHeight-$this->borderWidth,
		$background
		);

		if($this->backgroundImage) {
			// Calcul des nouvelles dimensions
			list($width, $height,$type) = getimagesize($this->backgroundImage);

			$new_width = $this->imageWidth-$this->borderWidth*2;
			$new_height = $this->imageHeight-$this->borderWidth*2;

			if($type === 1) {
				$type_ = 'gif';
			} elseif($type === 2) {
				$type_ = 'jpeg';
			} elseif($type === 3) {
				$type_ = 'png';
			} else {
				throw new Exception('Mauvais type pour l\'image de fond');
			}
			$fct = 'imagecreatefrom' . $type_;
			$imb = $fct($this->backgroundImage);


			imagecopyresampled(
			$im,
			$imb,
			$this->borderWidth,
			$this->borderWidth,
			0,
			0,
			$new_width,
			$new_height,
			$width,
			$height
			);

			imagedestroy($imb);
		}

		// couleur du texte
		$textColor = imagecolorallocate (
		$im,
		$this->textColor['R'],
		$this->textColor['V'],
		$this->textColor['B']
		);

		// centrage horizontal
		$x = ($this->imageWidth - $boxWidth)/2;
		// centrage vertical
		$y = $this->imageHeight   - $this->borderWidth - $this->fromBorder;

		// ombre
		if($this->shadow) {
			$shadow = imagecolorallocate(
			$im,
			$this->shadowColor['R'],
			$this->shadowColor['V'],
			$this->shadowColor['B']
			);
			imagettftext(
			$im,
			$this->fontSize,
			$this->textAngle,
			$x+$this->shadowX,
			$y+$this->shadowY,
			$shadow,
			$this->font,
			$text
			);
		}

		// le texte
		imagettftext(
		$im,
		$this->fontSize,
		$this->textAngle,
		$x,
		$y,
		$textColor,
		$this->font,
		$text
		);

		$this->makeHeaders();
		$image_function = 'image' . $this->type;
		$image_function($im);
		imagedestroy($im);
	}


	/**
	 * R�cup�re la cha�ne al�atoire g�n�r�e
	 *
	 * @return string cha�ne al�atoire g�n�r�e
	 */
	public function getRandString() {
		if(!$this->randString) {
			//$T = array_merge(range('a','z') , range('A', 'Z') , range(1,9));
			$T = array_merge(range(1,9)); // ne prend que des chiffres
			shuffle($T);
			//$TT = array_filter($T, array($this, 'forbiddenCharsFilter'));
			$TT = array_chunk($T, $this->stringLength);

			$this->randString = implode('', $TT[0]);
		}
		return $this->randString;
	}
	
	private function ImageRectangleWithRoundedCorners(&$im, $x1, $y1, $x2, $y2, $color, $radius) {
		// transparence
		$trans = imageColorAllocate ($im, 255, 255, 255);
		$color_ = imagecolortransparent($im, $trans);
		// rectangle sans coins
		imagefilledrectangle($im, $x1, $y1, $x2, $y2, $color_);
		imagefilledrectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $color);
		imagefilledrectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $color);
		// coins arrondis
		imagefilledellipse($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, $color);
		
	}

	private function forbiddenCharsFilter($in) {
		return in_array($in, $this->forbiddenChars);
	}

	private function setImageType($type) {
		switch(strtolower($type)) {
			case 'gif' :
			case 'png' :
			case 'jpeg' :
			$this->type = $type;
			break;
			case 'jpg' :
			$this->type = 'jpeg';
			break;
			default :
			$this->type = 'png';
		}
		if(!function_exists('image'.$this->type)) {
			throw new Exception('La fonction n\'est pas disponible');
		}
	}

	private function makeHeaders() {
		header('Expires: Mon, 01 Jan 2000 00:00:00 GMT');
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-Type: image/' . $this->type);
	}
}
?>