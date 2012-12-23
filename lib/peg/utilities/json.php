<?php
/**
 * @author Jefferson González
 * @license MIT
 * @link http://github.com/wxphp/peg Source code. 
*/

namespace Peg\Utilities;

/**
 * Encapsulates json functions to provide indentation in older
 * php versions as conversion from json to plain php arrays.
 */
class Json
{
	// Disable constructor
	private function __construct(){}
	
	/**
	 * Indents a flat JSON string to make it more human-readable.
	 * URL: http://recursive-design.com/blog/2008/03/11/format-json-with-php/
	 * @param string $json The original JSON string to process.
	 * @return string Indented version of the original JSON string.
	 */
	private static function Indent($json)
	{

		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = "\t";
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element, 
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element, 
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

	/**
	 * Equivalent of json_encode function but output pretty printed 
	 * json format to make it possible to edit the output manually.
	 * @param array $data
	 * @return string
	 */
	function Encode($data)
	{
		$data = json_encode($data);

		return self::Indent($data);
	}

	/**
	 * Equivalent to json_decode for json but with associative turned on.
	 * This function retreive json objects as associative array.
	 * @param string $data Json encoded data.
	 * @return array
	 */
	function Decode($data)
	{
		return json_decode($data, true);
	}
}

?>
