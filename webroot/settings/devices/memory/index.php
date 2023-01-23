<?php

use FSA\Neuron\HTML\Table,
    FSA\Neuron\HTML\ButtonLink;

require_once '../../../../vendor/autoload.php';
$response = App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$hwid = filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    require_once 'show.php';
    exit;
}
$response->showHeader('Список устройств в памяти');
?>
<p><a href="../" class="btn btn-primary">Вернуться к списку устройств</a></p>
<p><a href="../edit/" class="btn btn-primary">Добавить вручную</a></p>
<?php

$db_devices = App::deviceDatabase()->getAllHwid();
$storage_hwid = App::deviceStorage()->getAllHwid();
$mem_devices = array_flip($storage_hwid);
foreach ($db_devices as $db_device) {
    if (isset($mem_devices[$db_device])) {
        unset($storage_hwid[$mem_devices[$db_device]]);
    }
}
$table = new Table();
$table->setCaption('Обнаруженные устройства');
$table->addField('hwid', 'HWID');
$table->addField('description', 'Описание');
$table->addField('state', 'Информация');
$table->addField('updated', 'Было активно');
$table->addButton(new ButtonLink('Добавить', './?hwid=%s', 'hwid'));
$table->showTable(new class($storage_hwid)
{

    private $list;
    private $mem;
    private $count;

    public function __construct($list)
    {
        $this->list = $list;
        $this->count = count($list);
        $this->mem = App::deviceStorage();
    }

    public function fetchObject()
    {
        $hwid = array_shift($this->list);
        if (is_null($hwid)) {
            return null;
        }
        $device = $this->mem->get($hwid);
        $result = new \stdClass();
        $result->entity = $device;
        $result->hwid = $hwid;
        $result->description = $device->getDescription();
        $result->state = (string) $device;
        $date = $device->getLastUpdate();
        $result->updated = $date == 0 ? 'Нет данных' : date('d.m.Y H:i:s', $date);
        return $result;
    }

    public function rowCount()
    {
        return $this->count;
    }
});
$response->showFooter();
