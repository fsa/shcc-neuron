<?php

namespace HTML;

class Table {

    private $caption;
    private $fields=[];
    privaTE $rowCallback;
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

    public function addField($name, $description) {
        $this->fields[$name]=$description;
    }

    public function addButton($button) {
        $this->buttons[]=$button;
    }

    public function setButtonsSeparator($separator) {
        $this->buttons_separator=$separator;
    }

    public function setRowCallback(callable $func) {
        $this->rowCallback=$func;
    }

    public function showTable($statement): bool {
        if (!($row=$statement->fetch())) {
            return false;
        }
        $template=is_null($this->template)?new \Templates\Table():new $this->template;
        $template->caption=$this->caption;
        $template->fields=$this->fields;
        $template->style_row=$this->row_style_field;
        if (sizeof($this->buttons)>0) {
            $template->fields['buttons']='Действия';
        }
        $template->showHeader();
        do {
            if (sizeof($this->buttons)) {
                $row->buttons=$this->getButtons($row);
            }
            if(!is_null($this->rowCallback)) {
                call_user_func($this->rowCallback, $row);
            }
            $template->showRow($row);
        } while ($row=$statement->fetch());
        $template->showFooter();
        return true;
    }

    private function getButtons($row) {
        $buttons=[];
        foreach ($this->buttons AS $button) {
            $value=$row->{$button->getParamField()};
            if (!is_null($value)) {
                $buttons[]=$button->getHtml($value);
            }
        }
        return join($this->buttons_separator, $buttons);
    }

}
