<?php

/// 登陆服务下检查登陆
if (!isset($_SESSION['user_id'])) {
    temporarily_header_302(PROTOCOL . DOMAIN . '/');
    exit();
}