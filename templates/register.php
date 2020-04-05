<?php $this->layout('Common/layout', ['title' => 'Home']) ?>
<h1>Register</h1>

<form action="/register" method="POST"">
<label for="firstname">Firstname: <input type="text" name="firstname" /> </label>
<br/>
<label for="lastname">Lastname:&nbsp; <input type="text" name="lastname" /> </label>
<br/>
<label for="email" >Email: <input type="text" style="margin-left:2.5em;" name="email" /> </label>
<br/>
<label for="pwd">Password:&nbsp; <input type="password" name="pwd" /> </label>
<br/>
<input type="submit" value="Send" />
</form>
<span style="color:<?= $this->e($msgStyle); ?>"><?= $this->e($message); ?></span>
<br/>
<a href="/"><button>Back</button></a>
