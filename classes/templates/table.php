<?php

namespace Templates;

class Table{

    public $fields=[];
    public $caption;

    public function showHeader(){
?>
        <table>
<?php
        if (!is_null($this->caption)) {
?>
            <caption><?=$this->caption?></caption>
<?php
        }
?>
            <tr>
<?php
        foreach ($this->fields as $description) {
?>
                <th><?=$description?></th>
<?php
        }
?>
            </tr>
<?php
        }

        public function showRow($row){
?>
            <tr>
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
<?php
    }

}
