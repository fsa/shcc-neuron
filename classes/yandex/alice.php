<?php

namespace Yandex;

class Alice {

    private $meta;
    private $request;
    private $session;
    private $version;
    
    private $result;

    public function __construct($request) {
        $data=json_decode($request,true);
        $this->meta=$data['meta'];
        $this->request=$data['request'];
        $this->session=$data['session'];
        $this->version=$data['version'];
        $this->result['session']=['session_id'=>$this->session['session_id'],'message_id'=>$this->session['message_id'],'user_id'=>$this->session['user_id']];
        $this->resetResponce();
        $this->result['version']='1.0';
    }
    
    public function checkSkillId($skill_id) {
        if(!is_null($skill_id)) {
            if($this->session['skill_id']!=$skill_id) {
                throw new \AppException('Я не обрабатываю запросы для этого навыка!');            
            }
        }        
    }
    
    public function isNewDialog() {
        return $this->session['new']==1;
    }
    
    public function getRequest() {
        return $this->request;
    }
    
    public function getSession() {
        return $this->session;
    }
    
    public function setText($text,$tts=false) {
        $this->result['response']['text']=html_entity_decode($text);
        if($tts) {
            $this->result['response']['tts']=$tts;
        }
    }
    
    public function setEndSession(bool $value=true) {
        $this->result['response']['end_session']=$value;
    }
    
    public function resetResponce() {
        $this->result['response']=['end_session'=>false];
    }
    
    public function getResponse() {
        header('Content-Type: application/json');
        echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
    }
}
