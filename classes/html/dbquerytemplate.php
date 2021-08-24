<?php

namespace HTML;

interface DBQueryTemplate {

    public function setCaption(string $caption);

    public function showHeader();

    public function showRow($row);

    public function showEmpty();

    public function showFooter();
}
