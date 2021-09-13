<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class DeleteWebhook extends Query {

    private bool $drop_pending_updates;
    
    public function setDropPendingUpdates(bool $drop_pending_updates) {
        $this->drop_pending_updates=$drop_pending_updates;
    }

}
