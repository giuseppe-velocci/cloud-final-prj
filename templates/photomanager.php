<?php $this->layout('Common/layout', ['title' => 'Photo Manager', 'user' => $user]) ?>
<h1>Photo Manager</h1>

<form enctype="multipart/form-data" action="uploadfile" method="POST">
Select image to upload:
    <br/><br/>
    <input type="file" name="photo">
    <br/><br/>
    <input type="submit" value="Upload Image" name="submit">
</form>
<br/>
<span style="color:<?= $this->e($msgStyle); ?>"><?= $this->e($message); ?></span>
<br/>