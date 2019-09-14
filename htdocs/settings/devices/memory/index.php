<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$module=filter_input(INPUT_GET,'module');
$id=filter_input(INPUT_GET,'id');
if($module) {
    if($id) {
        require_once 'show.php';
        exit;
    }
    die;
}
HTML::showPageHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<?php
$devices=new SmartHome\DeviceList();
foreach ($devices->getModuleList() as $module) {
    $devices->query($module);
    $table=new HTML\Table();
    $table->setCaption('Модуль '.$module);
    $table->addField('id','ID');
    $table->addField('name','Наименование');
    $table->addField('status','Информация');
    $table->addField('updated','Был активен');
    $table->addButton(new HTML\ButtonLink('Подробности','./?module='.$module.'&id=%s'));
    $table->showTable($devices);
}
HTML::showPageFooter();
