<!-- links -->
<?php foreach($buttons AS $text => $url): ?>
    <a style="display:inline-block;" href="<?= $this->e($url) ?>"><button><?= $this->e($text) ?></button></a>
<?php endforeach; ?>


<!-- login/logout -->
<?php 
$action = is_null($user) ? 'Login' : 'Logout';
?>
<form style="display:inline-block; margin-left:3em;" action="/<?= strtolower($action) ?>" method="POST">
    <button><?= $action ?></button>
</form> 


<!-- register (if needed) -->
<?php if(is_null($user)): ?>
    <form style="display:inline-block;" action="/register" method="POST">
        <button>Register</button>
    </form> 
<?php endif; ?>


<!-- username -->
<?php if(! is_null($user)): ?>
    <p style="display:inline;">@<?= $this->e($user) ?></p>
<?php endif; ?>
<br/>