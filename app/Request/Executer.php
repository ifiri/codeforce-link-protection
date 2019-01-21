<?php

namespace TeaLinkProtection\App\Request;

use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\AdminInterface;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Contracts;

class Executer {
    private $Request = null;

    public function __construct(Contracts\IRequest $Request) {
        $this->Request = $Request;
    }

    public function execute_controller_action(Contracts\IController $Controller) {
        // depincly
        $ActionFactory = new ActionFactory();
        $Action = $ActionFactory->create_action_by($this->Request->action, $Controller);

        if(is_object($Action)) {
            $ConfigRepository = new Config\Repository;
            $Notices = new AdminInterface\Notices($ConfigRepository);

            // We can't doing redirect in Finally section
            // Because we should catch global exception in ExceptionHandler too
            try {
                $Action->execute($this->Request);
                $Action->do_redirect();
            } catch(Contracts\IException $Exception) {
                $LogWriter = new Log\Writer;
                $LogWriter->log($Exception->get_error_message());

                $Action->do_redirect();
            } catch(\Exception $Exception) {
                throw $Exception;
            }
        }
    }
}