<div class="row">
	<div class="col-10 offset-1 col-md-6 offset-md-3 card">
		<h2 class="text-center card-title">Forgot Password</h2>
		<form action="<?php echo URLROOT; ?>/users/password/forgot" method="post" class="col-sm-10 offset-sm-1 card-body small">

			<?php Messages::pop(); ?>

			<div class="field">
				<label for="email">Email</label>
				<input required type="email" class="form-control" name="email" id="email">
			</div><br>
			<input type="submit" name="submit" id="submit" value="Submit" class="form-control btn btn-danger"><br><br>
		</form>
	</div>
</div>