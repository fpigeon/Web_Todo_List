<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Pigeon ToDo List</title>
</head>
<body>
	<h1>TODO List</h1>
	<?php 
	//variables
	$todos = [];
	$file_path='data/todo.txt';

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
	        $filename='data/todo.txt';    
	    } //if user just hits enter

	    if (is_readable($filename)){
	        $handle = fopen($filename, 'r');
	        $contents = trim(fread($handle, filesize($filename)));
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
	
	
 	//go thru the file and add to the array	
	$file_items = open_file($file_path);
    if ($file_items !== FALSE){
	    foreach ($file_items as $list_item) {
	        array_push($todos, $list_item); //add to the end of the array
	    } //end of foreach
	} // add to the array if found

	//var_dump($_GET);
	var_dump($_POST);
	if (!empty($_POST['task'])){
		//trim($_POST['task']);
		//var_dump($_POST['task']);
		$newTodo = $_POST['task']; //assign the variable from the post
		$todos[] = $newTodo; // add to the array
		saveFile($file_path, $todos); // save your file
		header('Location: /todo_List.php');
		exit(0);
	}

	//remove item
	if (isset($_GET['remove_item']) ){
		 $removeItem = $_GET['remove_item'];
		 var_dump($removeItem);
		 unset($todos[$removeItem]);
		 saveFile($file_path, $todos); // save your file
		 header('Location: /todo_List.php');
		 exit(0);
	} //end of remove item
	//saveFile($file_path, $todos); // save your file

	//move uploaded files to the upload directory
	if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) {
    // Set the destination directory for uploads
    $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
    // Grab the filename from the uploaded file by using basename
    $filename = basename($_FILES['file1']['name']);
    // Create the saved filename using the file's original name and our upload directory
    $saved_filename = $upload_dir . $filename;
    // Move the file from the temp location to our uploads directory
    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
	} // end of upload files

	// Check if we saved a file
	if (isset($saved_filename)) {
	    // If we did, load todo items in to the todo arrays
	 	$saved_file_items = open_file($saved_filename);
	    foreach ($saved_file_items as $list_item) {
	        array_push($todos, $list_item); //add to the end of the array
	    } //end of foreach
	} // add to the array if found

	?>

	<!-- output array on screen -->
	<ul>
	<?php 
	
	foreach ($todos as $key => $todo) {
		//echo'<li>' . $todo . ' <a href="http://todo.dev/todo_List.php?remove_item{$key}">Remove Item</a>' . '</li>'. PHP_EOL;
		echo "<li>$todo <a href=\"http://todo.dev/todo_List.php?remove_item=$key\">Remove Item</a></li>\n";
	} // end of for each
	?>
	</ul>
	
	<h2>Input New Todo Items</h2>
	<form method="POST" action="/todo_List.php">
		<!-- <label for="item_num">Item Number: </label>
        <input id="item_num" name="item_num" type="text" placeholder="Todo Number">
        <br> -->
        <label for="task">Task</label>
        <input id="task" name="task" type="text" placeholder="Add Todo Item">
        <button type="submit">Add Item</button>
	</form>

	<h2>Upload File</h2>

	<form method="POST" enctype="multipart/form-data">
	    <label for="file1">File to upload: </label>
	    <input type="file" id="file1" name="file1">
		<br>
	    <input type="submit" value="Upload">
    
	</form>
	
	
</body>
</html>