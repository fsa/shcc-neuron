<?php

class Table {

    private $fields=[];
    private $buttons=[];
    private $caption;
    
    public function setCaption($caption) {
        $this->caption=$caption;
    }

    public function addField($name,$description) {
        $this->fields[$name]=$description;
    }

    public function addButton($link_name,$url,$link_data='id') {
        $this->buttons[]=[$url,$link_data,$link_name];
    }

    private function showTableHeader() {
        echo "<table>";
        if(!is_null($this->caption)) {
            echo "<caption>$this->caption</caption>";
        }
        echo "<tr>";
        foreach ($this->fields as $description) {
            echo "<th>$description</th>";
        }
        if (sizeof($this->buttons)>0) {
            echo "<th>Действия</th>";
        }
        echo "</tr>";
    }

    public function showTable($statement) {
        $this->showTableHeader();
        while ($row=$statement->fetch()) {
            echo "<tr>";
            foreach ($this->fields as $name=> $description) {
                echo "<td>".$row->$name."</td>";
            }
            if (sizeof($this->buttons)>0) {
                echo "<td>";
                $actions=[];
                foreach ($this->buttons AS $param) {
                    $value=$row->{$param[1]};
                    if (!is_null($value)) {
                        $actions[]='<a href="'.sprintf($param[0],$value).'">'.$param[2].'</a> ';
                    }
                }
                echo join('<br>',$actions);
                echo "</td>";
            }
            echo "</tr>";
        }
        $this->showTableFooter();
        $statement->closeCursor();
    }

    private function showTableFooter() {
        echo "</table>";
    }

    public function createCsv($statement,$filename) {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="export.csv";');
        $out=fopen($filename,'w');
        fputcsv($out,$this->fields);
        while ($row=$statement->fetch()) {
            $data=[];
            foreach ($this->fields as $name=> $description) {
                $data[]=$row->$name;
            }
            fputcsv($out,$data);
        }
    }

}
