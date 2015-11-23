<?php
class View {
	public function render($file, $data = array()) {
		ob_start ();

                if(!empty($data)){
                    foreach($data as $key => $value){
                        $$key = $value;
                    }
                }

		include ($file);
		$result = ob_get_contents ();
		ob_end_clean ();
		return $result;
	}
}