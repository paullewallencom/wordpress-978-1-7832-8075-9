<div class='home_list_item'>
	<div class='list_panel'>
        <?php foreach($data["records"] as $record){ ?>
		<div class='list_row'><a href=''><?php echo $record['title']; ?></a><?php do_action('wpwa_home_widgets_controls',$record['type'],$record['ID']); ?></div>
	<?php } ?>
	</div>	
</div>
