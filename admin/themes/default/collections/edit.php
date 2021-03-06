<?php
    $collectionTitle = strip_formatting(collection('Name'));
    if ($collectionTitle != '') {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = 'Edit Collection #' . collection('id') . $collectionTitle;
?>

<?php head(array('title'=> $collectionTitle, 'bodyclass'=>'collections')); ?>
<h1><?php echo $collectionTitle; ?></h1>

<?php echo delete_button(null, 'delete-collection', 'Delete this Collection', array(), 'delete-record-form'); ?>

<div id="primary">
	
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" class="submit" id="save-changes" value="Save Changes" />
</form>

</div>
<?php foot(); ?>
