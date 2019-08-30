<?php

class Table {

    private $caption;
    private $fields=[];
    private $buttons=[];
    private $buttons_separator='<br>';
    private $template;
    private $row_style_field;

    public function setTemplate(string $template) {
        $this->template=$template;
    }
    
    public function setCaption($caption) {
        $this->caption=$caption;
    }
    
    public function setRowStyleField($name) {
        $this->row_style_field=$name;
    }

    public function addField($name,$description) {
        $this->fields[$name]=$description;
    }

    public function addButton($link_name,$url,$link_data='id') {
        $this->buttons[]=[$url,$link_data,$link_name];
    }

    public function addButtonsSeparator($separator) {
        $this->buttons_separator=$separator;
    }

    public function showTable($statement) {
        $template=is_null($this->template)?new \Templates\Table():new $this->template;
        $template->caption=$this->caption;
        $template->fields=$this->fields;
        if (sizeof($this->buttons)>0) {
            $template->fields['buttons']='Действия';
        }
        $template->showHeader();
        while ($row=$statement->fetch()) {
            if (sizeof($this->buttons)>0) {
                $actions=[];
                foreach ($this->buttons AS $param) {
                    $value=$row->{$param[1]};
                    if (!is_null($value)) {
                        $actions[]=sprintf("<a href=\"$param[0]\">%s</a>",$value,$param[2]);
                    }
                }
                $row->buttons=join($this->buttons_separator,$actions);
            }
            $template->showRow($row, $this->row_style_field);
        }
        $template->showFooter();
    }

    private function showTableFooter() {
        echo "</table>";
    }

    public function createCsv($statement,$filename) {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
        $out=fopen('php://output','w');
        fputcsv($out,$this->fields);
        while ($row=$statement->fetch()) {
            $data=[];
            foreach ($this->fields as $name=>$description) {
                $data[]=$row->$name;
            }
            fputcsv($out,$data);
        }
    }

}
