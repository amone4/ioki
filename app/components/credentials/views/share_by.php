<div class="container">
	<?php Messages::pop();
	foreach ($data['encrypted'] as $key => $value) { ?>
		<div class="row"><strong><?php echo $value->link; ?></strong></div>
		<div class="row">
			<div class="col-6">
				<div class="row">ID: <?php echo $data['decrypted'][$key]['login']; ?></div>
				<div class="row">Password: <?php echo $value->password; ?></div>
				<div class="row">Status: <?php echo ($value->approved == 1 ? '' : 'Not ') . 'Approved'; ?></div>
			</div>
			<div class="col-6">
				<div class="row">Shared to: <?php echo $value->shared_to; ?></div>
				<div class="row">Shared till: <?php echo $value->shared_till; ?></div>
				<div class="row"><a href="<?php echo URLROOT; ?>/credentials/share/delete/<?php echo $value->id; ?>" class="btn btn-sm btn-danger">Delete</a></div>
			</div>
		</div><br>
	<?php } ?>
</div>