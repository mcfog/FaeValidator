<?php


class FaeValidator {
	const HELPER_CLASS = 'FaeHelper';

	private function __construct() {}
	private function compile($content) {
		$js = $php = '';
		$last_space = false;
		$tokens = token_get_all($content);
		foreach($tokens as $token) {
			if($in_ignore) {
				if($v[0]==T_DOC_COMMENT && $v[1]=='/**gx:compiler_ignore_end**/') $in_ignore = false;
				continue;
			}
			if(!is_array($token)) {
				if(in_array($token, array('+','-','*','/','%','<','>','?',':',','))) {
					$php .= $token;
					$js .= $token;
					$last_space = false;
				} else {
					throw new Exception('Fae syntax error.')
				}
			}
			list($tk, $v) = $v;
			switch($tk) {
				case T_COMMENT:
				case T_DOC_COMMENT:
					$last_space = false;
					break;
				case T_WHITESPACE:
					if (!$last_space) {
						$php .= ' ';
						$last_space = true;
					}
					break;
				case T_VARIABLE:
				//??
				case T_STRING:
					$php .= self::HELPER_CLASS."::$v[1]";
					$last_space = false;
					break;
				case T_IS_EQUAL:case T_IS_NOT_EQUAL:
				case T_IS_IDENTICAL:case T_IS_NOT_IDENTICAL:
				case T_IS_GREATER_OR_EQUAL:case T_IS_SMALLER_OR_EQUAL	
				case T_BOOLEAN_AND:case T_BOOLEAN_OR:
				case T_BREAK:case T_CONTINUE:
				case T_ELSE:case T_IF:
				case T_LNUMBER:case T_RETURN:
				case T_SL:case T_SR:
					$php .= $v;
					$js .= $v;
					
				default:
					throw new Exception('Fae syntax error');
					break;
			};
		}//end of foreach tokens
		while($in_class-->0) $php.='}';
		return $php;
	}
}