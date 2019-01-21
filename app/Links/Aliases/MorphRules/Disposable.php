<?php

namespace TeaLinkProtection\App\Links\Aliases\MorphRules;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Links\Aliases;
use TeaLinkProtection\App\Contracts;

class Disposable implements Contracts\IAliasMorphRule {
    private $link_id = null;
    
    public function __construct($Link) {
        $this->link_id = $Link->get_id();
    }

    public function is_morph_required($rule_value, $alias_params) {
        if($rule_value === 'on') {
            if($alias_params['TRIGGER_COUNT'] > 0) {
                return true;
            }
            
            // depincly
            $ConfigRepository = new Config\Repository;
            $AliasesRepository = new Aliases\Repository($ConfigRepository);
            
            $existing_aliases_for_passed_params = $AliasesRepository->get_by($this->link_id, $alias_params);

            if(!$existing_aliases_for_passed_params) {
                return true;
            }
        }
        
        return false;
    }
}