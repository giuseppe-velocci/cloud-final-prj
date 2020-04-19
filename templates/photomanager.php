<?php $this->layout('Common/layout', ['title' => 'Photo Manager', 'user' => $user]) ?>
<h1>Photo Manager</h1>

<form enctype="multipart/form-data" action="uploadfile" method="POST">
Select an image to upload:
    <br/><br/>
    <input type="file" name="photo">
    <br/><br/>
    <input type="submit" value="Upload Image" name="submit">
</form>
<br/>
<span style="color:<?= $this->e($msgStyle); ?>"><?= $this->e($message); ?></span>
<br/>

<?php if (count($images) < 1) :?>
    No images uploaded.

<?php else :?>
<h2>Manage Your Images</h2>
<form method="post" class="confirm-form" action="">
    <input class="confirm-input" type="submit" value="Delete" /> 
    <br/><br/>
    <?php foreach($images AS $img) :?>
        <a href="?"><?= $img->url ?></a> 
        Delete <input type="checkbox" value="<?= $img->url ?>" />
        <br/>
    <?php endforeach; ?>
</form>
<?php endif; ?>

<script src="./js/input.js"></script>