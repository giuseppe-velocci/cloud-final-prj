<!-- links -->
<?php foreach($buttons AS $text => $data): ?>
<?php if($data['public'] == true || ! is_null($user)) :?>
    <a style="display:inline-block;" href="<?= $this->e($data['path']) ?>">
        <button>
            <?= $this->e($text) ?>
        </button>
    </a>
<?php endif; ?>
<?php endforeach; ?>


<!-- login/logout -->
<?php 
$action = is_null($user) ? 'Login' : 'Logout';
?>
<a href="/<?= strtolower($action) ?>" style="display:inline-block; margin-left:3em;">
    <button>
        <?= $action ?>
    </button>
</a>


<!-- register (if needed) -->
<?php if(is_null($user)): ?>
    <a href="/register" style="display:inline-block;">
        <button>
            Register
        </button>
    </a> 
<?php endif; ?>


<!-- username -->
<?php if(! is_null($user)): ?>
    <p style="display:inline;">@<?= $this->e($user) ?></p>
<?php endif; ?>
<br/>