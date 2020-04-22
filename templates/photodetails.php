<?php $this->layout('Common/layout', ['title' => 'Photo Details', 'user' => $user]) ?>
<h1>Photo Details</h1>

<h3>Share with your friends: </h3>
<form action="photoshare" method="POST">
    <label for="expiry" style="font-weight:bold;">Expiry: <input type="date" name="expiry" /></label>
    <label for="expiry" style="font-weight:bold;">E-mail: <input type="text" name="email" /></label>
    <input type="hidden" name="filename" value="<?= $this->e($imgDetails->filename) ?>" />
    <input type="submit" value="Share this photo" />
</form>
<br/>

<img src="<?= $this->e($imgDetails->url) ?>" /> 

<br/><br/>
<b>Tags:</b>
<?php if(! empty($imgDetails->tags)): ?>
    <?php foreach($imgDetails->tags AS $tag): ?>
        <p><?= ucfirst(str_replace('_', ' ', $this->e($tag))) ?></p>
    <?php endforeach; ?>   
<?php else: ?>
    <p>No relevant tags detected.</p>
<?php endif; ?>

<br/> 
<b>Exif Data:</b>
<?php if(! empty($imgDetails->exif)): ?>
    <?php foreach($imgDetails->exif AS $exif): ?>
        <p><?= $this->e($exif) ?></p>
    <?php endforeach; ?>
<?php else: ?>
    <p>No exif data detected.</p>
<?php endif; ?>