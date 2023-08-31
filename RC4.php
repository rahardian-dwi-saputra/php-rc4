<?php

class RC4{
	private $key;
	
	public function set_key($key){
		$this->key = $key;
	}
	public function get_key(){
		return $this->key;
	}
	public function encrypt($plain){
		//inisialisasi S box
		$s = array();
		for($i=0; $i<256; $i++){
			$s[$i] = $i;
		}
		
		//permutasi pada S box
		$j = 0;
		for ($i = 0; $i < 256; $i++) {
			$j = ($j + $s[$i] + ord($this->key[$i % strlen($this->key)])) % 256;
			//swap S box
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
		}
		
		$i = 0;
		$j = 0;
		$cipher = '';
		for ($idx = 0; $idx < strlen($plain); $idx++){
			$i = ($i + 1) % 256;
			$j = ($j + $s[$i]) % 256;
			//swap S box
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
			//enkripsi
			$t = ($s[$i] + $s[$j]) % 256;
			$K = $s[$t];
			$cipher .= $plain[$idx] ^ chr($K);
		}
		return $cipher;
	}
	public function process_file_txt($path_file_input, $path_file_output, $encrypt){
		$file_input = fopen($path_file_input,"r");
		$file_output = fopen($path_file_output,"w");
		
		if($encrypt == true){
			$in = fread($file_input,filesize($path_file_input));
			$out = base64_encode($this->encrypt($in));
		}else{
			$in = base64_decode(fread($file_input,filesize($path_file_input)));
			$out = $this->encrypt($in);
		}
		
		fwrite($file_output, $out);
		fclose($file_output);
		fclose($file_input);
	}
}

$rc4 = new RC4();
$rc4->set_key('qwerty');

//enkripsi file txt
$rc4->process_file_txt("testfile.txt", "hasil_enkripsi.txt", true);

//dekripsi file txt
$rc4->process_file_txt("hasil_enkripsi.txt", "hasil_dekripsi.txt", false);



?>