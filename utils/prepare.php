<?php
	namespace utils;
	
	class Prepare {

		public static function cleanTxt($value) {
			return htmlspecialchars(strip_tags($value));
		}

		// UCfirst
		public static function UCfirst($texto, $encode = "UTF-8") {
			$resp = str_replace(",", "", $texto);
			$resp = mb_strtolower($resp, $encode);
			$resp = ucwords($resp);
			return $resp;
		}

		// Get Date
		public static function getDate() {
			return date("Y-m-d");
		}

		// Only Numbers
		public static function OnlyNumbers($mixed_input) {
			return filter_var($mixed_input, FILTER_SANITIZE_NUMBER_INT);
		}
	}
?>