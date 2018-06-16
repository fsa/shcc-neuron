<?php

namespace SmartHome;

class MeterHistory {
    
    private $place_id;
    private $meter_id;
    private $measure_id;
    private $from;
    private $to;

    public function __construct() {
        
    }
    
    public function setPlaceId($place_id, $measure_id=null) {
        $this->place_id=$place_id;
        $this->measure_id=$measure_id;
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
        if(!$this->meter_id and !$this->measure_id) {
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
        if($this->measure_id) {
            $where[]='measure_id=:measure_id';
            $params['measure_id']=$this->measure_id;
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
        $stmt=\DB::prepare('SELECT UNIX_TIMESTAMP(timestamp),value FROM meter_history WHERE '.join(' AND ',$where).$period);
        $stmt->execute($params);
        $rows=[];
        while($row=$stmt->fetch(\PDO::FETCH_NUM)) {
            $rows[]="[$row[0]000, $row[1]]";
        }
        return '['.join(',',$rows).']';
    }

    /**
     * Сохраняет данные в историю измерителей
     * @param type $sensors массив сенсоров устройства для сохранения в памяти
     * @param type $data ассоциативный массив имя_сенсора->значение
     */
    public static function addRecords($sensors,$data) {
        $stmt=\DB::prepare('INSERT INTO meter_history (meter_id,place_id,measure_id,value) VALUES (?,?,?,?)');
        foreach ($sensors as $sensor) {
            if (isset($data->{$sensor->property})) {
                $stmt->execute([$sensor->id,$sensor->place_id,$sensor->measure_id,$data->{$sensor->property}]);
            }
        }
        $stmt->closeCursor();
    }

}
