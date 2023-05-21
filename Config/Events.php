<?php

namespace Whatsapp\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper(["whatsapp_general", "whatsapp"]);
});