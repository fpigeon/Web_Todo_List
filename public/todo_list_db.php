<?php
/*
1. Create a database, and a table to hold your TODOs.
2. Update TODO list application to use MySQL instead of flat files.
3. Be sure to use prepared statements for all queries that could contain user input.
4. Add pagination. This should display 10 results per page, and when your list has over 10 records,
 there should be buttons to allow you to navigate forward and backwards through the "pages" of todos.
5. Abstract the MySQL connection and reusable functions to a class, if applicable.
*/
//classes
class InvalidInputException extends Exception { }
//constants
define ('LIMIT_VALUE', 10);
//variables 
$heading = ['id','task', 'action'];
$isValid = false; //form validation
$error_msg=''; //initailize variable to hold error messages

// Establish DB Connection
// Get new instance of PDO object
$dbc = new PDO('mysql:host=127.0.0.1;dbname=codeup_todo_db', 'frank', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";

// // Create the query and assign to var
// $query = 'CREATE TABLE todos (
//     id INT UNSIGNED NOT NULL AUTO_INCREMENT,
//     task VARCHAR(50) NOT NULL,    
//     PRIMARY KEY (id)
// )';
// // Run query, if there are errors they will be thrown as PDOExceptions
// $dbc->exec($query);

//validate string to be over zero and under 125 characters
function stringLengthCheck($string, $min=1, $max=125){
	if (strlen($string) <= $min || strlen($string) > $max) {
    			throw new InvalidInputException('$string must be over '.$min.' or under '.$max.' characters');
    } // end of excepmtion   
}//end of stringCheck

function getOffset(){
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	return ($page - 1) * 4;
} //end of getOffset

function getTodos($dbc){
	$stmt = $dbc->prepare('SELECT * FROM todos LIMIT :LIMIT OFFSET :OFFSET');
	$stmt->bindValue(':LIMIT', LIMIT_VALUE, PDO::PARAM_INT);
	$offset_value = getOffset();
	$stmt->bindValue(':OFFSET', $offset_value, PDO::PARAM_INT);
	$stmt->execute();
	$rows =  $stmt->fetchALL(PDO::FETCH_ASSOC);	
	return $rows;	
} //end of getUsers

//Check if something Posted
if(!empty($_POST)){		
	try {
		//ensure form entries are not empty
		foreach ($_POST as $value) {					
			stringLengthCheck($value);
		}  //end of foreach		

		// Get new instance of PDO object
		$dbc = new PDO('mysql:host=127.0.0.1;dbname=codeup_todo_db', 'frank', 'password');

		// Tell PDO to throw exceptions on error
		$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//a. is item being added => add todo!
		if ($_POST['task']){
			$stmt = $dbc->prepare('INSERT INTO todos (task)
	                       VALUES (:task)');		
		    $stmt->bindValue(':task', $_POST['task'], PDO::PARAM_STR);
		    $stmt->execute();
		    header('Location: /todo_list_db.php');
			exit(0);	
		} //end if POST addForm

		//b. is item being removed => remove todo
		if ($_POST['remove']){
			$stmt = $dbc->prepare('DELETE FROM todos WHERE id = :ID');		
		    $stmt->bindValue(':ID', $_POST['todoId'], PDO::PARAM_INT);
		    $stmt->execute();
		    header('Location: /todo_list_db.php');
			exit(0);
		} // end of if POST remove	
     	//c. *opt Is list being uploaded? => Add todos!

	} //end of try
	catch (InvalidInputException $e) {
		$error_msg = $e->getMessage().PHP_EOL;
	} // end of catch
}// end of if

//Query db for total todo count
$count = $dbc->query('SELECT count(*) FROM todos')->fetchColumn();

//Determine pagination values
$numPages = ceil($count / 4);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$nextPage = $page + 1;
$prevPage = $page - 1;

//Query for todos on current page
$todos = getTodos($dbc);

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/sites.css">	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">	
	<title>Database ToDo List</title>
</head>
<body>
	<div class="container">
		<h1>Database Todo List</h1>
		<!-- error message if present --> 
		<? if(!empty($error_msg)) : ?>
			<?= PHP_EOL . $error_msg . PHP_EOL;?>
			<script>alert('Something went wrong, try again');</script>
		<? endif; ?>	
		<table class="table table-striped table-hover">
				<!-- heading row -->
				<tr>			
					<? foreach ($heading as $value) :?>
						<th><?= $value ?> </th>								
					<? endforeach;  ?>			
				</tr>
				<!-- data from table -->
				<? foreach ($todos as $todo) :?>
				<tr>				
					<? foreach ($todo as $key => $todo_value): ?>
						<?= "<td>$todo_value</td>"; ?>					
					<? endforeach; ?>
					<td>
						<button class="btn btn-danger btn-sm pull-right btn-remove" data-todo="<?= $todo['id']; ?>">Remove</button>								
					</td>
				</tr>							
				<? endforeach; ?>				
		</table>
		<div id="pagination">
			<? if ($page == 1) : ?>
				<a class="btn-primary btn btn-lg" disabled="disabled" href="?page=<?= $prevPage; ?>" > &larr; Previous</a>
				<a class="btn btn-primary btn-lg active" href="?page=<?= $nextPage; ?>" >Next &rarr;</a>		
			<? elseif ($page == $numPages) : ?>
				<a class="btn btn-primary btn-lg active" href="?page=<?= $prevPage; ?>" > &larr; Previous</a>
				<a class="btn-primary btn btn-lg" disabled="disabled" href="?page=<?= $nextPage; ?>" >Next &rarr;</a>		
			<? else: ?>
				<a class="btn btn-primary btn-lg active" href="?page=<?= $prevPage; ?>" > &larr; Previous</a>
				<a class="btn btn-primary btn-lg active" href="?page=<?= $nextPage; ?>" >Next &rarr;</a>
			<? endif; ?>
		</div>			
		
		<h2>Input New Todo Items</h2>
		<form id="addForm" method="POST" action="/todo_list_db.php">		
	        <label for="task">Task</label>
	        <input id="task" name="task" type="text" placeholder="Add Todo Item">
	        <button type="submit" class="button">Add Item</button>
		</form>

		<h2>Upload File</h2>
		<form id="uploadForm" method="POST" enctype="multipart/form-data">
		    <label for="file1">File to upload: </label>
		    <input type="file" id="file1" name="file1">
			<br>
		    <input type="submit" value="Upload" class="button">    
		</form>

		<form id="removeForm" method="POST" action="/todo_list_db.php">
	    	<input id="removeId" type="hidden" name="remove" value="">
		</form>
	</div>
	
	<!-- JQuery -->	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>	
	<script>
		$('.btn-remove').click(function () {
	    	var todoId = $(this).data('todo');
	    	if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
	        	$('#remove-id').val(todoId);
	        	$('#remove-form').submit();
	    	} //end if
		}); //end of btn-remove
	</script>
	


</body>
</html>