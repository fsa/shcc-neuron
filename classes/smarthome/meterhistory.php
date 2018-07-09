<?php

namespace SmartHome;

class MeterHistory {
    
    private $place_id;
    private $meter_id;
    private $meter_unit_id;
    private $from;
    private $to;

    public function __construct() {
        
    }
    
    public function setPlaceId($place_id, $meter_unit_id=null) {
        $this->place_id=$place_id;
        $this->meter_unit_id=$meter_unit_id;
    }

    public function setMeterId($meter_id) {
        $this->meter_id=$meter_id;
    }
    
    public function setFromTimestamp($timestamp) {
        $this->from=$timestamp;
    }
    
    public function setFromDateTime($datetime) {
        $this->from=strtotime($datetime);
    }

    public function setToTimestamp($timestamp) {
        $this->to=$timestamp;
    }
    
    public function setToDateTime($datetime) {
        $this->to=strtotime($datetime);
    }

    public function getJson() {
        if(!$this->place_id and !$this->meter_id) {
            throw new Exception('Не задано место или измерительный прибор');
        }
        if(!$this->meter_id and !$this->meter_unit_id) {
            throw new Exception('Не задан тип измерительного прибора');
        }
        $params=[];
        $where=[];
        if($this->place_id) {
            $where[]='place_id=:place_id';
            $params['place_id']=$this->place_id;
        }
        if($this->meter_id) {
            $where[]='meter_id=:meter_id';
            $params['meter_id']=$this->meter_id;
        }
        if($this->meter_unit_id) {
            $where[]='meter_unit_id=:meter_unit_id';
            $params['meter_unit_id']=$this->meter_unit_id;
        }
        if($this->from) {
            $params['from']=date('c',$this->from);
            if($this->to) {
                $period=' AND timestamp BETWEEN :from AND :to';
                $params['to']=date('c',$this->to);
            } else {
                $period=' AND timestamp>=:from';
            }
        } else {
            $period='';
        }
        $stmt=\DB::prepare('SELECT UNIX_TIMESTAMP(timestamp)*1000,value FROM meter_history WHERE '.join(' AND ',$where).$period);
        $stmt->execute($params);
        $rows=$stmt->fetchAll(\PDO::FETCH_NUM);
        return json_encode($rows,JSON_NUMERIC_CHECK);
    }

    /**
     * Сохраняет данные в историю измерителей
     * @param type $sensors массив сенсоров устройства для сохранения в памяти
     * @param type $data ассоциативный массив имя_сенсора->значение
     */
    public static function addRecords($sensors,$data) {
        $stmt=\DB::prepare('INSERT INTO meter_history (meter_id,place_id,meter_unit_id,value) VALUES (?,?,?,?)');
        foreach ($sensors as $sensor) {
            if (isset($data->{$sensor->property})) {
                $stmt->execute([$sensor->id,$sensor->place_id,$sensor->meter_unit_id,$data->{$sensor->property}]);
            }
        }
        $stmt->closeCursor();
    }

}
