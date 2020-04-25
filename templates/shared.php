<?php 
    if (! is_null($user))
        $this->layout('Common/layout', ['title' => 'Photo Details', 'user' => $user]) 
?>
<h1>Photo Details</h1>

<?php 
    if(empty($imgDetails->shares)) {
        header('Location: /error404');
    } 
?>

<?php 
    $thisImg = $this->e($imgDetails->shares->{$this->e($guid)}); 
    $expiry  = substr($thisImg, strpos($thisImg, 'se=')+3, 10);

    if (strtotime($expiry) - time() <= 0) {
        header('Location: /error401');
    }
?>
<p>
    <b>Expires:</b> <?= $this->e($expiry);  ?>
</p>

<img src="<?= $thisImg ?>" /> 

<br/><br/>
<h3>Tags:</h3>
<?php if(! empty($imgDetails->tags)): ?>
    <?php foreach($imgDetails->tags AS $tag): ?>
        <p><?= ucfirst(str_replace('_', ' ', $this->e($tag))) ?></p>
    <?php endforeach; ?>   
<?php else: ?>
    <p>No relevant tags detected.</p>
<?php endif; ?>

<br/> 
<h3>Exif Data:</h3>
<?php if(! empty($imgDetails->exif)): ?>
    <?php $exifData = json_decode(json_encode($imgDetails->exif)); ?>
    <?php foreach($exifData AS $key => $exif): 
        $data = \App\Helper\ExifDataPrint::printExif($exif);
    ?>
        <b><?= $this->e($key); ?></b> 
        <p><?= $data ?></p>
    <?php endforeach; ?>
<?php else: ?>
    <p>No exif data detected.</p>
<?php endif; ?>