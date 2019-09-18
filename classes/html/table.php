<?php

namespace HTML;

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

    public function addButton($button) {
        $this->buttons[]=$button;
    }

    public function setButtonsSeparator($separator) {
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
        if (sizeof($this->buttons)>0) {
            while ($row=$statement->fetch()) {
                $buttons=[];
                foreach ($this->buttons AS $button) {
                    $value=$row->{$button->getParamField()};
                    if (!is_null($value)) {
                        $buttons[]=$button->getHtml($value);
                    }
                }
                $row->buttons=join($this->buttons_separator,$buttons);
                $template->showRow($row, $this->row_style_field);
            }
        } else {
            while ($row=$statement->fetch()) {
                $template->showRow($row, $this->row_style_field);
            }
        }
        $template->showFooter();
    }

}

