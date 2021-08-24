<?php

namespace HTML;

class TableView implements DBQueryTemplate {

    public $fields=[];
    public $buttons=[];
    public $caption;
    public $style_row;

    public function setCaption(string $caption) {
        $this->caption=$name;
    }

    public function setStyleField(string $name) {
        $this->style_row=$name;
    }

    public function addField($name, $description) {
        $this->fields[$name]=$description;
    }

    public function addButton($button) {
        $this->buttons[]=$button;
    }

    public function showHeader(){
?>
<div class="table-responsive">
    <table class="table table-striped table-hover table-sm">
<?php
        if (!is_null($this->caption)) {
?>
    <caption style="caption-side: top;"><?=$this->caption?></caption>
<?php
        }
?>
    <tr>
<?php
        $fields=$this->fields;
        if(sizeof($this->buttons)) {
            $fields['buttons']='Действия';
        }
        foreach ($fields as $description) {
?>
        <th class="table-bordered"><?=$description?></th>
<?php
        }
?>
    </tr>
<?php
    }

    public function showRow($row){
        $style=(!is_null($this->style_row) and !is_null($row->{$this->style_row}))?' '.$row->{$this->style_row}:'';
?>
        <tr class="table-bordered<?=$style?>">
<?php
        foreach (array_keys($this->fields) as $name) {
?>
            <td><?=$row->$name?></td>
<?php
        }
        if(sizeof($this->buttons)) {
?>
            <td><?=$this->getButtons($row)?></td>
<?php
        }
?>
        </tr>
<?php
    }

    public function showEmpty() {
        $this->showHeader();
?>
        <tr class="table-bordered"><td colspan="<?=sizeof($this->fields)?>">Нет данных</td></tr>
<?php
        $this->showFooter();
    }

    public function showFooter(){
?>
    </table>
</div>
<?php
    }

    private function getButtons($row) {
        $buttons=[];
        foreach ($this->buttons AS $button) {
            $value=$row->{$button->getParamField()};
            if (!is_null($value)) {
                $buttons[]=$button->getHtml($value);
            }
        }
        return join('<br>', $buttons);
    }

}