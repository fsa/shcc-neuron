<?php

namespace Templates;
class Forms {

    public static function formHeader($method, $url) {
?>
<form method="<?=$method?>" action="<?=$url?>">
<?php
    }

    public static function inputHidden($name, $value) {
?>
<input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
<?php
	}

    public static function inputInteger($name, $value, $label) {
?>
<div class="form-group row">
    <label for="<?= $name ?>" class="col-2 col-form-label"><?= $label ?></label>
    <div class="col-10">
            <input class="form-control" type="number" name="<?= $name ?>" id="<?= $name ?>" value="<?= $value ?>">
    </div>
</div>
<?php
    }

    public static function inputString($name, $value, $label=null) {
?>
<div class="form-group">
<?php
        if(!is_null($label)) {
?>
    <label for="<?= $name ?>"><?= $label ?></label>
<?php
        }
?>
    <input class="form-control" type="text" name="<?= $name ?>" id="<?= $name ?>" value="<?= htmlspecialchars($value) ?>">
</div>
<?php
    }

    public static function inputPassword($name, $value, $label=null) {
?>
<div class="form-group">
<?php
        if(!is_null($label)) {
?>
    <label for="<?= $name ?>"><?= $label ?></label>
<?php
        }
?>
    <input class="form-control" type="password" name="<?= $name ?>" id="<?= $name ?>" value="<?= htmlspecialchars($value) ?>">
</div>
<?php
    }

    public static function textarea($name, $value, $label=null) {
?>
<div class="form-group">
<?php
        if(!is_null($label)) {
?>
    <label for="<?= $name ?>"><?= $label ?></label>
<?php
        }
?>
    <textarea class="form-control" name="<?= $name ?>" id="<?= $name ?>" rows="3"><?= htmlspecialchars($value); ?></textarea>
</div>
<?php
    }

    public static function inputDate($name, $value, $label) {
        $date=$value?date("Y-m-d", strtotime($value)):'';
?>
<div class="form-group row">
  <label for="<?= $name ?>" class="col-2 col-form-label"><?=$label?></label>
  <div class="col-10">
	  <input class="form-control" type="date" name="<?= $name ?>" id="<?= $name ?>" value="<?=$date?>">
  </div>
</div>
<?php
    }
	
	
    public static function inputSelect($name,$value,$label,$statement) {
?>
<div class="form-group row">
    <label for="<?=$name?>" class="col-2 col-form-label"><?=$label?></label>
    <div class="col-10">
    <select class="form-control" name="<?=$name?>" id="<?=$name?>">
<?php
        while($row=$statement->fetch()) {
?>
        <option value="<?=$row[0]?>"<?=$row[0]==$value?' selected':''?>><?=$row[1]?></option>
<?php
        }
?>
    </select>
    </div>
</div>
<?php
    }
	
    public static function inputSelectArray($name,$value,$label,$array) {
?>
<div class="form-group">
    <label for="<?=$name?>"><?=$label?></label>
    <select class="form-control" name="<?=$name?>" id="<?=$name?>">
<?php
        foreach($array as $name=>$desc) {
?>
        <option value="<?=$name?>"<?=$name==$value?' selected':''?>><?=$desc?></option>
<?php
        }
?>
    </select>
</div>
<?php
    }

    public static function inputCheckbox($name,$value,$label) {
?>
<div class="form-group row">
    <label class="col-sm-2"></label>
    <div class="col-sm-10">
        <div class="form-check">
            <label class="form-check-label">
            <input type="checkbox" name="<?=$name?>" class="form-check-input"<?=$value?' checked':''?>><?=$label?>
            </label>
        </div>
    </div>
</div>
<?php
    }

    public static function submitButton($label,$value=null,$style="btn-primary") {
        if(is_null($value)) {
?>
<button type="submit" class="btn <?=$style?>"><?=$label?></button>
<?php
        } else {
?>
<button type="submit" name="action" value="<?=$value?>" class="btn <?=$style?>"><?=$label?></button>
<?php			 
        }
    }

    public static function formFooter() {
?>
</form>
<?php
    }

}
