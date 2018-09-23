<form class="container" method="POST" action="<?php echo URLROOT; ?>/credentials/share/add/<?php echo $data->id; ?>">
	<h3>Share</h3>
	<?php Messages::pop(); ?>
	<fieldset>
		<label for="username">Username:</label><br>
		<input type="text" id="username" name="username">
	</fieldset><br>
	<fieldset>
		<label for="shared_till_date">Shared till (date):</label><br>
		<input type="date" id="shared_till_date" name="shared_till_date">
	</fieldset><br>
	<fieldset>
		<label for="shared_till_time">Shared till (time):</label><br>
		<input type="time" id="shared_till_time" name="shared_till_time">
	</fieldset><br>
	<input type="submit" name="submit" value="Submit">
</form>
<br>
<div class="container">
	<div class="row">
		<h3>Credential being shared</h3>
	</div>
	<div class="row"><?php echo $data->link; ?></div>
	<div class="row"><?php echo $data->login; ?></div>
	<div class="row"><?php echo $data->password; ?></div>
</div>