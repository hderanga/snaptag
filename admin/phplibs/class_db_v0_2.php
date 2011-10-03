<?php

#------------------------- Class file for mySQL Database -------------------------

# Version : easyMySQL v0.2
# Written By Nazly Ahmed
# Mail To : me@nazly.net
# http://www.nazly.net
# Last Updated : 2004-08-17
# PHP 4.3.4

#---------------------------------------------------------------------------------

class mySqlDB
{
        var $dBaseKey;
		var $debugMode = false;
                
        //Default Constructor
        //Connect to mySQL Server
        //param1:mySQL server hostname:default-hostname
        //param2:Username:default-root
        //param3:Password:default-""
        function mySqlDB($hostName="localhost",$userName="root",$passWord=""){
        	$this->dBaseKey=mysql_connect($hostName,$userName,$passWord) or die(mysql_error());
        }

        //Select the Database
        //param1:Database name:defualt-test
        function dBSelect($dBase="test"){
        	mysql_select_db($dBase) or die(mysql_error());
        }

        //Closes the current opened database
        function dBClose(){//Closes a database connection while it's open        
         	mysql_close($this->dBaseKey);
        }
		
		function enableDebugMode(){
			$this->debugMode = true;
		}

        //Select Recordset - Return the ResultSet as an array
        //param1:Table Name
        //param2:Field name(s) to be selected as an array
        //param3:Fields name(s) to be sorted by as an array (optional)
        //param4:Sort By (A|D):default-A [A:Ascending,D:Descending](optional)
        //param5:Condition List:default=""(optional)
        //       syntax=>
        //       "field='value'"
        //       "field1='value1' AND field2='value2'"
        //       "field1='value1' OR field2='value2'"
        //param6:Limit Start
        //param7:No of records from the limit start
        function querySelect($tableName,$arrFields,$sort="",$sortBy="A",$arrCon="",$lStart=0,$numRecs=0){
        	$fNames=implode(",",$arrFields);
         	if(!$arrCon){
          		$query="select $fNames from $tableName";
         	}
         	else{
          		$query="select $fNames from $tableName where $arrCon";
         	}
         	if($sort){
          		$strSort=implode(",",$sort);
          		$query.=" order by $strSort";
          		if($sortBy=="D"){
           			$query.=" desc";
          		}
         	}
         	if($numRecs){
          		$query.=" limit $lStart,$numRecs";
         	}
			if($this->debugMode){
				echo "<pre>{$query}</pre>";
			}
			else{
				$result=mysql_query($query);
				$resultSet=array();
				if($result){				
					$count=0;
					while($row=mysql_fetch_assoc($result)){					
						foreach($row as $field=>$fieldVal){
							$resultSet[$count][$field]=$fieldVal;
						}
						$count++;
					}
					mysql_free_result($result);
					return $resultSet;
				}
				else{
					echo mysql_error();
				}
			}
        }
                
                
		//Insert or Update Records
		//param1:Table Name
        //param2:Field name(s) and values to be added as an array which field names are the keys and value to be added 
        //param3:Update/Insert (optional) true - update : false-insert
		//param4:Condtion (optional
        function queryUpdate($tblName,$insArr,$cond=false,$condStr=""){
         	$arrInKeys=array_keys($insArr);
         	$fNString=implode(",",$arrInKeys);
         	$fValString="'".implode("','",$insArr)."'";
         	if($cond){
          		$fUpString="";
          		for($i=0;$i<count($arrInKeys);$i++){
           			$arrKeyId=$arrInKeys[$i];
           			$fUpString.=$arrKeyId."='$insArr[$arrKeyId]'";
           			if($i!=(count($arrInKeys)-1)){
            			$fUpString.=",";
           			}
          		}
          		if($condStr){
           			$sqlString="update $tblName set $fUpString where $condStr";
          		}
          		else{
           			$sqlString="update $tblName set $fUpString";
          		}
         	}
         	else{
          		$sqlString="insert into $tblName ($fNString) values ($fValString)";
         	}
			if($this->debugMode){
				echo "<pre>{$sqlString}</pre>";
			}
			else{
				$queryVal=mysql_query($sqlString);
				if(!$queryVal){
					echo mysql_error();
				}
			}
        }

        //Delete Records Records
        //param1:Table Name
        //param2:Condition (optional)
        function queryDelete($tblName,$condStr=""){
        	if($condStr){
          		$sqlStr="delete from $tblName where $condStr";
         	}
         	else{
          		$sqlStr="delete from $tblName";
         	}
			if($this->debugMode){
				echo "<pre>{$sqlStr}</pre>";
			}
			else{
				$queryVal1=mysql_query($sqlStr);
				if(!$queryVal1){
					echo mysql_error();
				}
			}
        }

        function countNumRec($tableName,$field,$condStr=""){
			if($condStr){
				$sqlStr="select count($field) from $tableName where $condStr";
			}
			else{
				$sqlStr="select count($field) from $tableName";
			}
			if($this->debugMode){
				echo "<pre>{$sqlStr}</pre>";
			}
			else{
				$queryVal1=mysql_query($sqlStr);
				if(!$queryVal1){
					echo mysql_error();
				}
				else{
					return mysql_result($queryVal1,0,0);
				}
			}
        }

        function sumOfField($tableName,$field,$condStr=""){
			if($condStr){
				$sqlStr="select sum($field) from $tableName where $condStr";
			}
			else{
				$sqlStr="select sum($field) from $tableName";
			}
			if($this->debugMode){
				echo "<pre>{$sqlStr}</pre>";
			}
			else{
				$queryVal1=mysql_query($sqlStr);
				if(!$queryVal1){
					echo mysql_error();
				}
				else{
					return mysql_result($queryVal1,0,0);
				}
			}
        }
                
		function queryManual($query){
			if($this->debugMode){
				echo "<pre>{$query}</pre>";
			}
			else{
				$result=mysql_query($query);
				if($result){
					return $result;
				}
				else{
					echo mysql_error();
				}
			}
        }

		function mySQLSafe($str){			
			return mysql_real_escape_string($str);
		}
		
		function getResultSet($result,$fields){
			$retArr=array();
			foreach($result as $qRecord){
				$arrRec=array();
				foreach($fields as $key=>$val){
					$arrRec[$key]=$qRecord[$val];
				}
				$retArr[]=$arrRec;
			}
			return $retArr;
		}
}

?>
