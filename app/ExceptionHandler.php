<?php

namespace TeaLinkProtection\App;

use TeaLinkProtection\App\Log;

class ExceptionHandler {
    // Catching all exceptions
    public function handle(\Exception $Exception) {
        // depincly
        $LogWriter = new Log\Writer;
        $LogWriter->log($Exception->getMessage());
    }
}