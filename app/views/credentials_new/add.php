<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<form class="container" method="POST" action="">
		<?php dequeMessages(); ?>
		<fieldset>
			<label for="link">Link:</label><br>
			<input type="text" id="link" name="link">
		</fieldset><br>
		<fieldset>
			<label for="login">Login:</label><br>
			<input type="text" id="login" name="login">
		</fieldset><br>
		<fieldset>
			<label for="password">Password:</label><br>
			<input type="password" id="password" name="password">
		</fieldset><br>
		<fieldset>
			<label for="type">Type:</label><br>
			<input type="radio" name="type" value="username" checked>Username<br>
			<input type="radio" name="type" value="Email">Email
		</fieldset><br>
		<input type="submit" name="submit">
	</form>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>