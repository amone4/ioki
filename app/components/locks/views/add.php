<form class="container" method="POST" action="<?php echo URLROOT; ?>/locks/add">
	<?php Messages::pop(); ?>
	<fieldset>
		<label for="id">Lock ID:</label><br>
		<input type="text" id="id" name="id">
	</fieldset><br>
	<fieldset>
		<label for="name">Name:</label><br>
		<input type="text" id="name" name="name">
	</fieldset><br>
	<input type="submit" name="submit" value="Submit">
</form>