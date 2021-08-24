<?php

namespace Templates\Settings;

class Users implements \HTML\DBQueryTemplate {

    public $caption;

    public function setCaption(string $caption) {
        $this->caption=$caption;
    }

    public function showHeader(){
?>
<div class="card">
    <div class="card-header text-center"><?=$this->caption?></div>
<?php
    }

    public function showRow($row) {
?>
    <div class="card">
    <div class="card-body<?=$row->disabled?' bg-danger text-white':''?>">
<?=$row->name?> &nbsp; <a href="./edit/?uuid=<?=$row->uuid?>" class="btn btn-sm btn-light">Редактировать</a><br>
Логин: <?=$row->login?><?=$row->scope?' &nbsp; Права доступа: '.trim($row->scope,'{}'):''?><?=$row->groups?' &nbsp; Группы: '.trim($row->groups,'{}'):''?><br>
Еmail: <?=$row->email?><br>
    </div>
    </div>
<?php
    }

    public function showEmpty() {
        $this->showHeader();
?>
    <div class="card">
        <div class="card-body">Пользователей не найдено</div>
    </div>
<?php
        $this->showFooter();
    }

    public function showFooter(){
?>
</div>
<?php
    }

}