<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/forms.css">

	<div class="row">
		<div class="col-10 offset-1 col-md-6 offset-md-3 card">
			<h2 class="text-center card-title">Change Password</h2>
			<form action="<?php echo URLROOT; ?>/users/change" method="post" class="col-sm-10 offset-sm-1 card-body small">

				<?php dequeMessages(); ?>

				<div class="field">
					<label for="old">Old Password</label>
					<input required type="password" class="form-control" name="old" id="old">
				</div>
				<div class="field">
					<label for="new">New Password</label>
					<input required type="password" class="form-control" name="new" id="new">
				</div>
				<div class="field">
					<label for="re">Re-Enter Password</label>
					<input required type="password" class="form-control" name="re" id="re">
				</div>
				<input type="submit" name="submit" id="submit" value="Submit" class="form-control btn btn-danger"><br><br>
			</form>
		</div>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>