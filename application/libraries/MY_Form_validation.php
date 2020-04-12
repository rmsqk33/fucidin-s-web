<?php

class MY_Form_validation extends CI_Form_validation{
    public function is_alpha_numeric_special($str, $field){
        $pattern = "[^0-9a-zA-Z`~!@#$%^&*()_+-=|\\\'\";:,.<>/]";
        return !preg_match($pattern, $str);
    }

    protected function _build_error_msg($line, $field = '', $param = ''){
		$message = parent::_build_error_msg($line, $field, $param);
        $message = $this->convert_relation_grammar($message, array("{을}", "{를}"), "을", "를");
        $message = $this->convert_relation_grammar($message, array("{이}", "{가}"), "이", "가");
        $message = $this->convert_relation_grammar($message, array("{과}", "{와}"), "과", "와");
        $message = $this->convert_relation_grammar($message, array("{은}", "{는}"), "은", "는");

        return $message;
	}

    private function convert_relation_grammar($message, $search, $support, $nonSupport){
        $pos = 0;
        while(true){
            $continue = false;
            foreach($search as $str){
                $pos = strpos($message, $str, $pos);
                if($pos != false){
                    $continue = true;

                    $utf8todec = ord($message[$pos - 3]) - 0xea;
                    $utf8todec = $utf8todec == 0 ? ord($message[$pos - 2]) - 0xb0 : ord($message[$pos - 2]) % 0x80 + 0x10 + ($utf8todec - 1) * 0x40;
                    $utf8todec = ($utf8todec * 0x40 ) + ord($message[$pos - 1]) % 0x80;

                    if($utf8todec % 28 == 0){
                        $message = substr_replace($message, $nonSupport, $pos, strlen($str));
                    } else{
                        $message = substr_replace($message, $support, $pos, strlen($str));
                    }

                    $pos++;
                }
            }

            if(!$continue)
                break;
        }

        return $message;
    }
}
