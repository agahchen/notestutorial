<?php

namespace OCA\MyApp\Controller\MyController;

use OCP\AppFramework\Controller;
use OCP\Files\IAppData;
use OCP\IRequest;

class MyController extends Controller {
    /** @var IAppData */
    private $appData;

    public function __construct($appName,
                                IRequest $request,
                                IAppData $appData) {
        parent::__construct($appName, $request);
        $this->appData = $appData;
    }

    public function createFolder($name) {
        $this->appData.newFolder($name);
    }
}