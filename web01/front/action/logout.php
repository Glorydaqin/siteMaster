<?php

session_unset();
temporarily_header_302(PROTOCOL . DOMAIN);

exit();