<?php

namespace Templates;

class Table{

    public $fields=[];
    public $caption;
    public $style_row;

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
        foreach ($this->fields as $description) {
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
            foreach ($this->fields as $name=> $description) {
?>
                <td><?=$row->$name?></td>
<?php
            }
?>
            </tr>
<?php
        }

        public function showFooter(){
?>
        </table>
    </div>
<?php
    }

}
