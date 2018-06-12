<?php

namespace Templates;

class HTML {

    public function header() {
?>
<html>
<head>
<title><?=$this->title?></title>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th,td {
        border: solid 1px black;
        padding: 0.3rem;
    }
</style>
</head>
<body>
<?php
    }

    public function footer() {
?>
</body>
</html>
<?php        
    }

}
