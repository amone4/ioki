<div class="container">
	<?php Messages::pop();
	foreach ($data as $lock) { ?>
		<div class="row"><strong><?php echo $lock->name; ?></strong></div>
		<div class="row">
			<div class="col-6">
				<div class="row">Shared by: <?php echo $lock->shared_by; ?></div>
				<div class="row">Shared till: <?php echo $lock->shared_till; ?></div>
			</div>
			<div class="col-6">
				<div class="row">Status: <?php echo ($lock->approved == 1 ? '' : 'Not ') . 'Approved'; ?></div>
				<?php if ($lock->approved == 0) : ?>
				<div class="row">
					<a href="<?php echo URLROOT; ?>/locks/share/approve/<?php echo $lock->id; ?>" class="btn btn-sm btn-success">Approve</a><br>
					<a href="<?php echo URLROOT; ?>/locks/share/reject/<?php echo $lock->id; ?>" class="btn btn-sm btn-danger">Reject</a>
				</div>
				<?php endif ?>
			</div>
		</div><br>
	<?php } ?>
</div>