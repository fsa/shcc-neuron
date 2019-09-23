<?php

namespace Templates;

class Table{

    public $fields=[];
    public $func=[];
    public $caption;

    public function showHeader(){
?>
        <table class="table table-striped table-hover table-sm table-responsive">
<?php
        if (!is_null($this->caption)) {
?>
            <caption style="caption-side: top;"><?=$this->caption?></caption>
<?php
        }
?>
            <tr>
<?php
        foreach ($this->fields as $description) {
?>
                <th class="table-bordered"><?=$description?></th>
<?php
        }
?>
            </tr>
<?php
        }

        public function showRow($row,$style_field=''){
            $style=(!is_null($style_field) and !is_null($row->$style_field))?' '.$row->$style_field:'';
?>
            <tr class="table-bordered<?=$style?>">
<?php
            foreach ($this->fields as $name=> $description) {
                $text=($name=='buttons' or is_null($this->func[$name]))?$row->$name:$this->func[$name]($row);
?>
                <td><?=$text?></td>
<?php
            }
?>
            </tr>
<?php
        }

        public function showFooter(){
?>
        </table>
<?php
    }

}
