<br><div class="container">
	<a href="<?php echo URLROOT; ?>">Home</a> |
	<?php if (!validateLogin()) : ?>
		<a href="<?php echo URLROOT; ?>/users">Login</a> |
		<a href="<?php echo URLROOT; ?>/users/register">Register</a>
	<?php else : ?>
		<a href="<?php echo URLROOT; ?>/credentials">Manage Credentials</a> |
		<a href="<?php echo URLROOT; ?>/users/logout">Logout</a>
	<?php endif ?>
</div><br>