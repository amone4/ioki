<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/forms.css">

<div class="row">
	<div class="col-10 offset-1 col-md-6 offset-md-3 card">
		<h2 class="text-center card-title">Change Password</h2>
		<form action="<?php echo URLROOT; ?>/users/password/change" method="post" class="col-sm-10 offset-sm-1 card-body small">

			<?php Messages::pop(); ?>

			<div class="field">
				<label for="oldPassword">Old Password</label>
				<input required type="password" class="form-control" name="oldPassword" id="oldPassword">
			</div>
			<div class="field">
				<label for="newPassword">New Password</label>
				<input required type="password" class="form-control" name="newPassword" id="newPassword">
			</div>
			<div class="field">
				<label for="onfirmPassword">Confirm Password</label>
				<input required type="password" class="form-control" name="confirmPassword" id="confirmPassword">
			</div>
			<input type="submit" name="submit" id="submit" value="Submit" class="form-control btn btn-danger"><br><br>
		</form>
	</div>
</div>