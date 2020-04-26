<html>
<head>
    <title><?=$this->e($title)?></title>
</head>
<body>
    <?php 
        // default links in navbar
        $btn['Home'] = ['path' => '/', 'public' => true];
        $btn['Dashboard'] = ['path' => '/dashboard', 'public' => false];
        $btn['Photo Manager'] = ['path' => '/photomanager', 'public' => false]; 
        $btn['Photo Maps'] = ['path' => '/photomaps', 'public' => false]; 
        $this->insert('Common/navbar', ['buttons' => $btn, 'user' => $user]); 
    ?>
    <?=$this->section('content')?>
</body>
</html>