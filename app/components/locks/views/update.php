<form class="container" method="POST" action="<?php echo URLROOT; ?>/locks/update/<?php echo $data->id; ?>">
	<?php Messages::pop(); ?>
	<fieldset>
		<label for="name">Name:</label><br>
		<input type="text" id="name" name="name" value="<?php echo $data->name; ?>">
	</fieldset><br>
	<input type="submit" name="submit" value="Submit">
</form>