<?
//include classes
require_once('classes/filestore.php');
//classes
class InvalidInputException extends Exception { }
//variables
$todos = [];  //array to hold todo item
$error_msg=''; //initailize variable to hold error messages

//create a new data store based on filestore
$todo_store = new Filestore('data/todo.txt');

//go thru the local text file and add to the array
$file_items = $todo_store->read();

if ($file_items !== FALSE){
    foreach ($file_items as $list_item) {
        array_push($todos, $list_item); //add to the end of the array
    } //end of foreach
} // end of if

//add new todo items from POST
try{
	if (!empty($_POST['task'])){	
		$newTodo = $_POST['task']; //assign the variable from the post
		if (strlen($newTodo) == 0 || strlen($newTodo) > 240) {
	    	throw new InvalidInputException('$newTodo must be over 0 or under 240 characters');
	    } //end of exemption
		$todos[] = $newTodo; // add to the array	
		$todo_store->write($todos); // save your file
		header('Location: /todo_List.php');
		exit(0);
	} //end of add item
} // end of try
catch(InvalidInputException $e){
	$error_msg = $e->getMessage().PHP_EOL;
} // end of catch



//remove item from todo array using GET
if (isset($_GET['remove_item']) ){
	 $removeItem = $_GET['remove_item'];	 
	 unset($todos[$removeItem]); //remove from todo array	 
	 $todo_store->write($todos); // save your file
	 header('Location: /todo_List.php');
	 exit(0);
} //end of remove item
	
//move uploaded files to the upload directory	
if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) {
	if ($_FILES['file1']['type'] == 'text/plain'){
		$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	    // Grab the filename from the uploaded file by using basename
	    $filename = basename($_FILES['file1']['name']);
	    // Create the saved filename using the file's original name and our upload directory
	    $saved_filename = $upload_dir . $filename;
	    // Move the file from the temp location to our uploads directory
	    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);

	    //add items to the todo list	    
	    $saved_file_items = $todo_store->read($saved_filename);
	    foreach ($saved_file_items as $list_item) {
	        array_push($todos, $list_item); //add to the end of the array
	    } //end of foreach	    
	    $todo_store->write($todos); // save your file	
	} // Set the destination directory for uploads
    else{
    	$error_msg = 'Upload error: wrong file type. Must be .txt';
    } 
} //end of if to move uploaded files
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/sites.css">
	<title>Pigeon ToDo List</title>
</head>
<body>
	<h1>TODO List</h1>	 
	<? if(!empty($error_msg)) : ?>
		<?= PHP_EOL . $error_msg . PHP_EOL;?>
		<script>alert('Something went wrong, try again');</script>
	<? endif; ?>	
	
	<!-- output array on screen -->
	<ul>	 	
		<? foreach ($todos as $key => $todo) :?>
		<!-- sanitize user input -->
		<? $todo = htmlspecialchars(strip_tags($todo)); ?>
		<?= "<li>$todo <a href=\"http://todo.dev/todo_List.php?remove_item=$key\">Remove Item</a></li>\n"; ?>
		<? endforeach; ?>
	</ul>
	
	<h2>Input New Todo Items</h2>
	<form method="POST" action="/todo_List.php">		
        <label for="task">Task</label>
        <input id="task" name="task" type="text" placeholder="Add Todo Item">
        <button type="submit" class="button">Add Item</button>
	</form>

	<h2>Upload File</h2>
	<form method="POST" enctype="multipart/form-data">
	    <label for="file1">File to upload: </label>
	    <input type="file" id="file1" name="file1">
		<br>
	    <input type="submit" value="Upload" class="button">    
	</form>	
</body>
</html>