<?php
echo head();
$item = get_item_by_id($digi->item_id);
?>
<div id="primary">
    <h1>Afbeelding: <?php echo $digi->pid;?></h1>
    <h2><?php echo item('Dublin Core', 'Title',array(),$item);?></h2>   
</div>

<?php
echo foot();
?>
