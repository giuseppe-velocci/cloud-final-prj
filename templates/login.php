<?php $this->layout('Common/layout', ['title' => 'Login', 'user' => $user]) ?>
<h1>Login</h1>

<form action="/login" method="POST"">
<label for="email" >Email: <input type="text" style="margin-left:2.5em;" name="email" /> </label>
<br/>
<label for="pwd">Password:&nbsp; <input type="password" name="pwd" /> </label>
<br/>
<input type="submit" value="Login" />
</form>
<span style="color:<?= $this->e($msgStyle); ?>"><?= $this->e($message); ?></span>
<br/>
<a href="/"><button>Back</button></a>
