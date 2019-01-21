<?php

namespace TeaLinkProtection\App\Request\Actions;

use TeaLinkProtection\App\AdminInterface;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Security;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class UpdateLink implements Contracts\IAction {
    private $nonce_alias = 'update-link';

    public function __construct(Contracts\IController $Controller) {
        
    }

    public function execute(Contracts\IRequest $Request) {
        // depincly
        $ConfigRepository = new Config\Repository;
        $Notices = new AdminInterface\Notices($ConfigRepository);
        $LinksRepository = new Links\Repository($ConfigRepository);

        try {
            if(!Libraries\Utils::verify_nonce_for($this->nonce_alias, $Request)) {
                throw new Exceptions\IncorrectNonceException('Incorrect Nonce');
            }

            $LinksRepository->update_by($Request);

            $Notices->add_notice_to_stack('link-update-success');
        } catch(Exceptions\LinkUpdateFailException $Exception) {
            $Notices->add_notice_to_stack('link-update-fail');

            throw $Exception;
        } catch(Contracts\IException $Exception) {
            $Notices->add_notice_to_stack('something-wrong-with-action');

            throw $Exception;
        }
    }

    public function do_redirect() {
        wp_redirect(admin_url('admin.php?page=tlp-links'));
        exit;
    }
}