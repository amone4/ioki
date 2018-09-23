<?php defined('_INDEX_EXEC') or die('Restricted access'); ?>

<br><div class="container">
	<a href="<?php echo URLROOT; ?>">Home</a> |
	<?php if (!Misc::validateLogin()) : ?>
		<a href="<?php echo URLROOT; ?>/users">Login</a> |
		<a href="<?php echo URLROOT; ?>/users/register">Register</a>
	<?php else : ?>
		<a href="<?php echo URLROOT; ?>/credentials">Manage Credentials</a> |
		<a href="<?php echo URLROOT; ?>/users/password/change">Change Password</a> |
		<a href="<?php echo URLROOT; ?>/users/logout">Logout</a>
	<?php endif ?>
</div><br>