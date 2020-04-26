<?php $this->layout('Common/layout', ['title' => 'Photo Details', 'user' => $user]) ?>
<h1>Photo Details</h1>

<h3>Share with your friends: </h3>

<form action="/photoshare" method="POST">
    <label for="expiry" style="font-weight:bold;">Expiry: <input type="date" name="expiry" /></label>
    <input type="hidden" name="filename" value="<?= $this->e($imgDetails->filename) ?>" />
    <input type="submit" value="New shareable link" />
</form>
<span style="color:<?= $this->e($msgStyle); ?>"><?= $this->e($message); ?></span>

<?php if(! empty($imgDetails->shares)):  ?>
    <?php foreach ($imgDetails->shares AS $k => $v):  ?>
        <p>
            <?php $link = $this->e($sharePath) . $this->e($k); ?>
            <b>Shereable link:</b> <a target="_blank" href="<?= $link ?>"><?= $link ?></a>
            &nbsp;<b>Expires:</b> <?= $this->e($v); ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>
<br/>

<img src="<?= $this->e($imgDetails->url) ?>" /> 

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
<?php $exifData = \App\Helper\ExifDataHelper::exifString2Array($imgDetails->exif); ?>
    <?php 
    foreach($exifData AS $key => $exif): ?>
        <p><?= $exif ?></p>
    <?php endforeach; ?>
<?php else: ?>
    <p>No exif data detected.</p>
<?php endif; ?>