<div class="row">
	<div class="col-10 offset-1 col-md-6 offset-md-3 card">
		<h2 class="text-center card-title">Reset Password</h2>
		<form action="<?php echo URLROOT . '/users/password/reset/' . $data; ?>" method="post" class="col-sm-10 offset-sm-1 card-body small">

			<?php Messages::pop(); ?>

			<div class="field">
				<label for="password">New Password</label>
				<input required type="password" class="form-control" name="password" id="password">
			</div>
			<div class="field">
				<label for="confirmPassword">Re-Enter Password</label>
				<input required type="password" class="form-control" name="confirmPassword" id="confirmPassword">
			</div>
			<input type="submit" name="submit" id="submit" value="Submit" class="form-control btn btn-danger"><br><br>
		</form>
	</div>
</div>