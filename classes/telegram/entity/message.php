<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Message extends AbstractEntity {

    public int $message_id;
    public ?User $from=null;
    public int $date;
    public Chat $chat;
    public ?User $forward_from=null;
    public ?Chat $forward_from_chat=null;
    public ?int $forward_from_message_id=null;
    public ?string $forward_signature=null;
    public ?string $forward_sender_name=null;
    public ?int $forward_date=null;
    public ?Message $reply_to_message=null;
    public ?User $via_bot=null;
    public ?int $edit_date=null;
    public ?string $media_group_id=null;
    public ?string $author_signature=null;
    public ?string $text=null;
    public ?array $entities=null;
    public ?Animation $animation=null;
    public ?Audio $audio=null;
    public ?Document $document=null;
    public ?array $photo=null;
    public ?Sticker $sticker=null;
    public ?Video $video=null;
    public ?VideoNote $video_note=null;
    public ?Voice $voice=null;
    public ?string $caption=null;
    public ?array $caption_entities=null;
    public ?Contact $contact=null;
    public ?Dice $dice=null;
    public ?Game $game=null;
    public ?Poll $poll=null;
    public ?Venue $venue=null;
    public ?Location $location=null;
    public ?array $new_chat_members=null;
    public ?User $left_chat_member=null;
    public ?string $new_chat_title=null;
    public ?array $new_chat_photo=null;
    public ?bool $delete_chat_photo=null;
    public ?bool $group_chat_created=null;
    public ?bool $supergroup_chat_created=null;
    public ?bool $channel_chat_created=null;
    public ?int $migrate_to_chat_id=null;
    public ?int $migrate_from_chat_id=null;
    public ?Message $pinned_message=null;
    public ?Invoice $invoice=null;
    public ?SuccessfulPayment $successful_payment=null;
    public ?string $connected_website=null;
    public ?PassportData $passport_data=null;
    public ?ProximityAlertTriggered $proximity_alert_triggered=null;
    public ?InlineKeyboardMarkup $reply_markup=null;

    protected function parseArray($key, $value) {
        $result=[];
        switch ($key) {
            case 'entities':
                foreach ($value as $entity) {
                    $result[]=new MessageEntity($entity);
                }
                break;
            case 'photo':
                foreach ($value as $entity) {
                    $result[]=new PhotoSize($entity);
                }
                break;
            case 'caption_entities':
                foreach ($value as $entity) {
                    $result[]=new MessageEntity($entity);
                }
                break;
            case 'new_chat_members':
                foreach ($value as $entity) {
                    $result[]=new User($entity);
                }
                break;
            case 'new_chat_photo':
                foreach ($value as $entity) {
                    $result[]=new PhotoSize($entity);
                }
                break;
            default:
        }
        return $result;
    }

}