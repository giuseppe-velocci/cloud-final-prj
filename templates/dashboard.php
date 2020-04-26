<?php $this->layout('Common/layout', ['title' => 'Dashboard', 'user' => $user]) ?>
<h1>Personal Dashboard</h1>
<form method="GET">
    <h3>Filter images for:</h3>
    <label for="tag">Tag: 
        <input type="text" name="tag" value="<?= isset($_GET['tag']) ? $this->e($_GET['tag']) :'' ?>" placeholder="tag to find" />
        <input type="text" name="exif" value="<?= isset($_GET['exif']) ? $this->e($_GET['exif']) :'' ?>" placeholder="exif to find" />
    </label>
    <input type="submit" value="Search" />
</form>

<?php 
    foreach($images AS $img): 
    $link = str_replace('.', '%20', $this->e($img->filename));
?>
    <div style="display:inline-block; margin:1em; maxwidth:150px; vertical-align: text-top;"> 
        <p><a href="/photodetails/<?= $link ?>"><?= $img->filename ?></a></p>
        <img height="150" width="auto" src="<?= $this->e($img->url) ?>" />
        <p>Uploaded: <?= date('Y-m-d', $this->e($img->_id->getTimestamp())) ?></p>
        <?php 
            $tagLen = count($img->tags);
            if($tagLen > 0): ?>
            <p>Tags: 
            <?php foreach ($img->tags AS $key => $tag): ?>
                <?= str_replace('_', ' ', $this->e($tag)); ?>
                <?php $key < $tagLen -1 ? ',' : ''; ?>
            <?php endforeach; ?> 
            </p>
        <?php endif; ?>
        
    </div>
<?php endforeach; ?>
