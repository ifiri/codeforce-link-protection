<?php

namespace TeaLinkProtection\App\Links\PermissionsValidation\ValidationRules;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class Disposable implements Contracts\ILinkPermissionValidationRule {
    // Ссылка одноразовая, умирает после одного срабатывания.
    // Поэтому доступна для просмотра всегда и всем.
    public function apply($link_rule) {
        return true;
    }
}