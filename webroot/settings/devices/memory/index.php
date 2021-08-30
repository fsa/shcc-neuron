<?php

require_once '../../../common.php';
Session::grantAccess([]);
$hwid=filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    require_once 'show.php';
    exit;
}
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<p><a href='../edit/'>Добавить вручную</a></p>
<?php

$db_devices=SmartHome\Devices::getDevicesHwids();
$storage_hwids=SmartHome\DeviceStorage::getDevicesHwids();
$mem_devices=array_flip($storage_hwids);
foreach ($db_devices as $db_device) {
    if (isset($mem_devices[$db_device])) {
        #unset($storage_hwids[$mem_devices[$db_device]]);
    }
}
$memdevitable=new HTML\Table();
$memdevitable->setCaption('Новые устройства в сети');
$memdevitable->addField('hwid', 'HWID');
$memdevitable->addField('description', 'Описание');
$memdevitable->addField('state', 'Информация');
$memdevitable->addField('updated', 'Было активено');
$memdevitable->addButton(new HTML\ButtonLink('Добавить', './?hwid=%s', 'hwid'));
$memdevitable->showTable(new class($storage_hwids) {

    private $list;
    private $mem;
    private $count;

    public function __construct($list) {
        $this->list=$list;
        $this->count=count($list);
        $this->mem=new \SmartHome\DeviceStorage;
    }

    public function fetchObject() {
        $hwid=array_shift($this->list);
        if (is_null($hwid)) {
            return null;
        }
        $device=$this->mem->get($hwid);
        $result=new \stdClass();
        $result->entity=$device;
        $result->hwid=$hwid;
        $result->description=$device->getDescription();
        $result->state=(string) $device;
        $date=$device->getLastUpdate();
        $result->updated=$date==0?'Нет данных':date('d.m.Y H:i:s', $date);
        return $result;
    }

    public function rowCount() {
        return $this->count;
    }
});
httpResponse::showHtmlFooter();
