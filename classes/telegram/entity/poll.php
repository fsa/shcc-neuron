<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Poll extends AbstractEntity {

    public string $id;
    public string $question;
    public array $options;
    public int $total_voter_count;
    public bool $is_closed;
    public bool $is_anonymous;
    public string $type;
    public bool $allows_multiple_answers;
    public ?int $correct_option_id=null;
    public ?string $explanation=null;
    public ?array $explanation_entities=null;
    public ?int $open_period=null;
    public ?int $close_date=null;

    protected function parseArray($key, $value) {
        $result=[];
        switch ($key) {
            case 'options':
                foreach ($value as $entity) {
                    $result[]=new PollOption($entity);
                }
                break;
            case 'explanation_entities':
                foreach ($value as $entity) {
                    $result[]=new MessageEntity($entity);
                }
                break;
        }
        return $result;
    }

}
