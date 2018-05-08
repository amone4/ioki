<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<div class="container">
		<?php dequeMessages();
		foreach ($data['encrypted'] as $key => $value) { ?>
			<div class="row"><strong><?php echo $value->link; ?></strong></div>
			<div class="row">
				<div class="col-6">
					<div class="row"><?php echo $value->login; ?></div>
					<div class="row"><?php echo $value->password; ?></div>
				</div>
				<div class="col-6">
					<div class="row">
						<a href="<?php echo URLROOT; ?>/credentials_new/update/<?php echo $value->id; ?>" class="btn btn-primary col-4">Update</a>
						<a href="<?php echo URLROOT; ?>/credentials_new/delete/<?php echo $value->id; ?>" class="btn btn-danger col-4">Delete</a>
						<a href="<?php echo URLROOT; ?>/credentials_new/share/add/<?php echo $value->id; ?>" class="btn btn-warning col-4">Share</a>
					</div>
				</div>
			</div><br>
		<?php } ?>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>