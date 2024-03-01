<?php

namespace UI;

use Service\IService\IAccountService;
use Service\IService\IUserService;
use UI\Window\LoginWindow;

class NewUI {
    private IUserService $userService;
    private IAccountService $accountService;
    
    public function __construct(IUserService $userService, IAccountService $accountService)  {
        $this->userService = $userService;
        $this->accountService = $accountService;
    }

    public function run() : void {
        $window = new LoginWindow();

        while ($window != null) {
            $window->setUserService($this->userService);
            $window->setAccountService($this->accountService);

            $window = $window->run();
        }
    }
}