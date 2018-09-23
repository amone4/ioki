<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<div class="row">
		<div class="col-10 offset-1 col-md-6 offset-md-3 card">
			<h2 class="text-center card-title">Register</h2>
			<form action="<?php echo URLROOT; ?>/users/register" method="post" class="col-sm-10 offset-sm-1 card-body small">

				<?php Messages::pop(); ?>

				<div class="field">
					<label for="name">Name</label>
					<input required type="text" class="form-control" name="name" id="name"><br>
				</div>
				<div class="field">
					<label for="email">Email</label>
					<input required type="email" class="form-control" name="email" id="email"><br>
				</div>
				<div class="field">
					<label for="username">Username</label>
					<input required type="text" class="form-control" name="username" id="username"><br>
				</div>
				<div class="field">
					<label for="phone">Phone Number</label>
					<input required type="number" max="9999999999" min="1000000000" class="form-control" name="phone" id="phone"><br>
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input required type="password" class="form-control" name="password" id="password"><br>
				</div>
				<div class="field">
					<label for="confirm">Confirm Password</label>
					<input required type="password" class="form-control" name="confirmPassword" id="confirmPassword"><br>
				</div>

				<input type="submit" name="submit" id="submit" value="Register" class="form-control btn btn-success"><br><br>
				<div class="col-md-8 offset-md-2 text-center"><a href="<?php echo URLROOT; ?>/users">Already registered? Login</a></div>

			</form>
		</div>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>