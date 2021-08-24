<?php

namespace HTML;

class DBQuery {

    private $template;
    private $callback;

    public function __construct(DBQueryTemplate $template) {
        $this->template=$template;
    }

    public function setCaption(string $caption) {
        $this->template->setCaption($caption);
    }

    public function setRowFunction(callable $func) {
        $this->callback=$func;
    }

    public function show($statement): int {
        if (!($row=$statement->fetchObject())) {
            $this->template->showEmpty();
            return 0;
        }
        $this->template->showHeader();
        do {
            if(!is_null($this->callback)) {
                call_user_func($this->callback, $row);
            }
            $this->template->showRow($row);
        } while ($row=$statement->fetchObject());
        $this->template->showFooter();
        return $statement->rowCount();
    }

}
