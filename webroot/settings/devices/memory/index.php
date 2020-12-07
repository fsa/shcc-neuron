<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$hwid=filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    require_once 'show.php';
    exit;
}
httpResponse::showHtmlHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<p><a href='../edit/'>Добавить вручную</a></p>
<?php

$db_devices=SmartHome\Devices::getDevicesHwids();
$mem_list=SmartHome\MemoryStorage::getDevicesHwids();
$mem_devices=array_flip($mem_list);
foreach ($db_devices as $db_device) {
    if (isset($mem_devices[$db_device])) {
        unset($mem_list[$mem_devices[$db_device]]);
    }
}
$memdevitable=new HTML\Table();
$memdevitable->setCaption('Новые устройства в сети');
$memdevitable->addField('hwid', 'HWID');
$memdevitable->addField('description', 'Описание');
$memdevitable->addField('state', 'Информация');
$memdevitable->addField('updated', 'Было активено');
$memdevitable->addButton(new HTML\ButtonLink('Добавить', './?hwid=%s', 'hwid'));
$memdevitable->showTable(new class($mem_list) {

    private $list;
    private $mem;

    public function __construct($list) {
        $this->list=$list;
        $this->mem=new \SmartHome\MemoryStorage;
    }

    public function fetch() {
        $hwid=array_shift($this->list);
        if (is_null($hwid)) {
            return null;
        }
        $device=$this->mem->getDevice($hwid);
        $result=new \stdClass();
        $result->entity=$device;
        $result->hwid=$hwid;
        $result->description=$device->getDescription();
        $result->state=(string) $device;
        $date=$device->getLastUpdate();
        $result->updated=$date==0?'Нет данных':date('d.m.Y H:i:s', $date);
        return $result;
    }
});
httpResponse::showHtmlFooter();
