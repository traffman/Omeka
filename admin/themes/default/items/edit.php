<?php
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != '[Untitled]') {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = 'Edit Item #' . item('id') . $itemTitle;
?>
<?php head(array('title'=> $itemTitle, 'bodyclass'=>'items primary','content_class' => 'vertical-nav'));?>
<h1><?php echo $itemTitle; ?></h1>
<?php echo delete_button(null, 'delete-item', 'Delete this Item', array(), 'delete-record-form'); ?>
<?php include 'form-tabs.php'; // Definitions for all the tabs for the form. ?>

<div id="primary">

    <form method="post" enctype="multipart/form-data" id="item-form" action="">
        <?php include 'form.php'; ?>
        <div>
            <?php echo submit(array('name'=>'submit', 'id'=>'save-changes', 'class'=>'submit'), 'Save Changes'); ?>
        </div>
    </form>

</div>

<?php foot();?>
