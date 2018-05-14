<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<form class="container" method="POST" action="<?php echo URLROOT; ?>/credentials/update/<?php echo $data->id; ?>">
		<?php dequeMessages(); ?>
		<fieldset>
			<label for="login">Login:</label><br>
			<input type="text" id="login" name="login" value="<?php echo $data->login; ?>">
		</fieldset><br>
		<fieldset>
			<label for="password">Password:</label><br>
			<input type="password" id="password" name="password" value="<?php echo $data->password; ?>">
		</fieldset><br>
		<fieldset>
			<label for="type">Type:</label><br>
			<input type="radio" name="type" value="username"<?php echo ($data->type ? '' : ' checked'); ?>>Username<br>
			<input type="radio" name="type" value="email"<?php echo ($data->type ? ' checked' : ''); ?>>Email
		</fieldset><br>
		<input type="submit" name="submit" value="Submit">
	</form>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>