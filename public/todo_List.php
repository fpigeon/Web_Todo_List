<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Pigeon ToDo List</title>
</head>
<body>
	<h1>TODO List</h1>
	<ul>
	<?php 
	//functions

	function saveFile($filename, $list_array){
	    if($filename == ''){
	        $filename='data/default.txt';    
	    } //if user just hits enter
	    $handle = fopen($filename, 'w');
	    if (is_writeable($filename)){        
	        foreach ($list_array as $list_item) {
	            fwrite($handle, $list_item . PHP_EOL);
	        }//end of foreach
	        fclose($handle);
	        return TRUE;
	    } //end of ovewrite ok
	    else {
	        return FALSE;
	    } // end of else
	} //end of SaveFile

	function open_file($filename){
	    if($filename == ''){
	        $filename='todo.txt';    
	    } //if user just hits enter

	    if (is_readable($filename)){
	        $handle = fopen($filename, 'r');
	        $contents = fread($handle, filesize($filename));
	        fclose($handle);
	        //echo $contents;    
	        $arrayed = explode(PHP_EOL, $contents);
	        return $arrayed; 
	    }//end of file found
	    else {
	        echo 'Error Reading File' . PHP_EOL;
	        return FALSE;
	    }//file not found


	    
	}//end of open file

	//$todos = ['eggs', 'milk', 'soda'];
	$todos = [];
	// echo "Enter the path and file name: ";
 	$file_path='todo.txt';
            //$file_items = open_file($file_path);
	$file_items = open_file($file_path);
    if ($file_items !== FALSE){
	    foreach ($file_items as $list_item) {
	        array_push($todos, $list_item); //add to the end of the array
	    } //end of foreach
	} // add to the array if found
	?>
	<ul>
	<?php 
	foreach ($todos as  $todo) {
		echo'<li>' . $todo . '</li>' . PHP_EOL;
	} // end of for each
	?>
	</ul>
	
	<h2>Input New Todo Items</h2>
	<form method="POST">
		<!-- <label for="item_num">Item Number: </label>
        <input id="item_num" name="item_num" type="text" placeholder="Todo Number">
        <br> -->
        <label for="task">Task</label>
        <input id="task" name="task" type="text" placeholder="Add Todo Item">
        <button type="submit">Add Item</button>
	</form>
	<?php
		var_dump($_GET);
		var_dump($_POST);
	?>
</body>
</html>