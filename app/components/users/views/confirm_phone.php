<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<div class="row">
		<div class="col-10 offset-1 col-md-6 offset-md-3 card">
			<h2 class="text-center card-title">Verify Phone Number</h2>
			<form action="<?php echo URLROOT; ?>/users/confirm/phone" method="post" class="col-sm-10 offset-sm-1 card-body small">

				<?php Messages::pop(); ?>

				<div class="field">
					<label for="otp">Enter OTP</label>
					<input required type="text" class="form-control" name="otp" id="otp">
				</div>
				<input type="submit" name="submit" id="submit" value="Submit" class="form-control btn btn-danger"><br><br>
			</form>
		</div>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>