<?php require_once APPROOT . '/views/inc/header.php'; ?>

	<div class="container">
		<?php dequeMessages();
		foreach ($data['encrypted'] as $key => $value) { ?>
			<div class="row"><strong><?php echo $value->link; ?></strong></div>
			<div class="row">
				<div class="col-6">
					<div class="row">ID: <?php echo $data['decrypted'][$key]['login']; ?></div>
					<div class="row">Shared by: <?php echo $value->shared_by; ?></div>
					<div class="row">Shared till: <?php echo $value->shared_till; ?></div>
				</div>
				<div class="col-6">
					<div class="row">Status: <?php echo ($value->approved == 1 ? '' : 'Not ') . 'Approved'; ?></div>
					<?php if ($value->approved == 0) : ?>
					<div class="row">
						<a href="<?php echo URLROOT; ?>/credentials/share/approve/<?php echo $value->id; ?>" class="btn btn-sm btn-success">Approve</a><br>
						<a href="<?php echo URLROOT; ?>/credentials/share/reject/<?php echo $value->id; ?>" class="btn btn-sm btn-danger">Reject</a>
					</div>
					<?php endif ?>
				</div>
			</div><br>
		<?php } ?>
	</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>