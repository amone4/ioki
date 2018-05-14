<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<div class="row">
		<div class="col-10 offset-1 col-md-6 offset-md-3 card">
			<h2 class="text-center card-title">Login</h2>

			<?php dequeMessages(); ?>

			<form method="post" action="<?php echo URLROOT; ?>/users" class="col-sm-10 offset-sm-1 card-body small">
				<div class="field">
					<label for="username">Username</label>
					<input required type="text" name="username" class="form-control" id="username">
				</div><br>
				<div class="field">
					<label for="password">Password</label>
					<input required type="password" name="password" class="form-control" id="password">
				</div><br>
				<input type="submit" name="submit" value="Login" class="form-control btn btn-success"><br><br>
				<div class="col-md-8 offset-md-2 text-center">
					<a href="<?php echo URLROOT; ?>/users/register">Register</a> |
					<a href="<?php echo URLROOT; ?>/users/password/forgot">Forgot password</a>
				</div>
			</form>

		</div>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>