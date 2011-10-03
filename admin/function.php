<?php
function seofriendlyurl($certTitle){
				$certTitle	= strtolower($certTitle);
				$certTitle = str_replace(' _ ','_',$certTitle);
				$certTitle = str_replace(', ','_',$certTitle);
				$certTitle = str_replace(" – ","-",$certTitle);
				$certTitle = str_replace("…","",$certTitle);
				$certTitle = str_replace('&','and',$certTitle);
				$certTitle = str_replace(' ','-',$certTitle);
				$certTitle = str_replace('_','_',$certTitle);
				$certTitle = str_replace('!','',$certTitle);
				$certTitle = str_replace('?','',$certTitle);
				$certTitle = str_replace(".","_",$certTitle);	
				$certTitle = str_replace('/','_',$certTitle);		
				$certTitle = str_replace('(','_',$certTitle);		
				$certTitle = str_replace(')','_',$certTitle);
				$certTitle = str_replace(';','_',$certTitle);
				$certTitle = str_replace(',','_',$certTitle);		
				$certTitle = str_replace(':','_',$certTitle);
				$certTitle = str_replace('|','_',$certTitle);
				$certTitle = str_replace('- ','-',$certTitle);
				$certTitle = str_replace(' -','-',$certTitle);
				$certTitle = str_replace("'","",$certTitle);
				$certTitle = str_replace("' ","",$certTitle);
				$certTitle = str_replace("’","",$certTitle);
				$certTitle = str_replace('"','',$certTitle);
				$certTitle = str_replace('--','-',$certTitle);
				$certTitle = str_replace('---','-',$certTitle);
				return $certTitle;

}


?>