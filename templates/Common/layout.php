<html>
<head>
    <title><?=$this->e($title)?></title>
</head>
<body>
    <?php 
        // default links in navbar
        $btn['Home'] = '/';
        $this->insert('Common/navbar', ['buttons' => $btn, 'user' => $user]); 
    ?>
    <?=$this->section('content')?>
</body>
</html>