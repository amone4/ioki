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
						<a href="<?php echo URLROOT; ?>/credentials/update/<?php echo $value->id; ?>" class="btn btn-primary col-4">Update</a>
						<a href="<?php echo URLROOT; ?>/credentials/delete/<?php echo $value->id; ?>" class="btn btn-danger col-4">Delete</a>
						<a href="<?php echo URLROOT; ?>/credentials/share/add/<?php echo $value->id; ?>" class="btn btn-warning col-4">Share</a>
					</div>
				</div>
			</div><br>
		<?php } ?>
		<div class="row">
			<a href="<?php echo URLROOT; ?>/credentials/add" class="btn btn-primary">Add new Credential</a>
			<a href="<?php echo URLROOT; ?>/credentials/share/by" class="btn btn-primary">Credentials shared by you</a>
			<a href="<?php echo URLROOT; ?>/credentials/share/to" class="btn btn-primary">Credentials shared to you</a>
		</div>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>