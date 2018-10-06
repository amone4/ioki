<div class="container">
	<?php Messages::pop();
	foreach ($data as $lock) { ?>
		<div class="row"><strong><?php echo $lock->name; ?></strong></div>
		<div class="row">
			<div class="col-6">
				<div class="row">
					<a href="<?php echo URLROOT; ?>/locks/update/<?php echo $lock->id; ?>" class="btn btn-primary col-4">Update</a>
					<a href="<?php echo URLROOT; ?>/locks/delete/<?php echo $lock->id; ?>" class="btn btn-danger col-4">Delete</a>
					<a href="<?php echo URLROOT; ?>/locks/share/add/<?php echo $lock->id; ?>" class="btn btn-warning col-4">Share</a>
				</div>
			</div>
		</div><br>
	<?php } ?>
	<div class="row">
		<a href="<?php echo URLROOT; ?>/locks/add" class="btn btn-primary">Add new Lock</a>
		<a href="<?php echo URLROOT; ?>/locks/share/by" class="btn btn-primary">Locks shared by you</a>
		<a href="<?php echo URLROOT; ?>/locks/share/to" class="btn btn-primary">Locks shared to you</a>
	</div>
</div>