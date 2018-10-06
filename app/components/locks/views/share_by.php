<div class="container">
	<?php Messages::pop();
	foreach ($data as $lock) { ?>
		<div class="row"><strong><?php echo $lock->name; ?></strong></div>
		<div class="row">
			<div class="col-6">
				<div class="row">Status: <?php echo ($lock->approved == 1 ? '' : 'Not ') . 'Approved'; ?></div>
			</div>
			<div class="col-6">
				<div class="row">Shared to: <?php echo $lock->shared_to; ?></div>
				<div class="row">Shared till: <?php echo $lock->shared_till; ?></div>
				<div class="row"><a href="<?php echo URLROOT; ?>/locks/share/delete/<?php echo $lock->id; ?>" class="btn btn-sm btn-danger">Delete</a></div>
			</div>
		</div><br>
	<?php } ?>
</div>