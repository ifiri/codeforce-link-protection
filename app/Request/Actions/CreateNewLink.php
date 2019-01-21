<?php

namespace TeaLinkProtection\App\Request\Actions;

use TeaLinkProtection\App\AdminInterface;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Security;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class CreateNewLink implements Contracts\IAction {
    private $nonce_alias = 'create-new-link';

    private $ConfigRepository = null;

    public function __construct(Contracts\IController $Controller) {
        $this->ConfigRepository = new Config\Repository;
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

            $inserted_link_id = $LinksRepository->save_by($Request);

            $Notices->add_notice_to_stack('link-add-success');
        } catch(Exceptions\LinkCreateFailException $Exception) {
            $Notices->add_notice_to_stack('link-add-fail');

            if($inserted_link_id) {
                $LinksRepository->delete_by($inserted_link_id);
            }

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