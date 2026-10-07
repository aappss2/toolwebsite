<?php
// Constants for new platform

define('APP_NAME', 'PromptTai Tools');
define('APP_DOMAIN', 'https://tool.prompttai.com');
define('OLD_DOMAIN', 'https://jdiro.com');

define('FREE_USES_DEFAULT', 10);
define('FREE_USES_ANON_DAILY', 2);

define('SESSION_TIMEOUT', 7200); // 2 hours
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour

define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_ALLOWED_EXT', ['pdf','jpg','jpeg','png','webp','gif','doc','docx','xls','xlsx','ppt','pptx','txt','csv','mp3','wav','mp4','mov']);
define('UPLOAD_ALLOWED_MIME', [
    'application/pdf',
    'image/jpeg','image/png','image/webp','image/gif',
    'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain','text/csv',
    'audio/mpeg','audio/wav',
    'video/mp4','video/quicktime'
]);

// Paths
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('DATABASE_PATH', ROOT_PATH . '/database');

// Roles
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');

// Subscription statuses
define('SUB_STATUS_ACTIVE', 'active');
define('SUB_STATUS_CANCELLED', 'cancelled');
define('SUB_STATUS_EXPIRED', 'expired');
define('SUB_STATUS_PAST_DUE', 'past_due');
define('SUB_STATUS_PENDING', 'pending');

// Tool statuses
define('TOOL_STATUS_ACTIVE', 'active');
define('TOOL_STATUS_DISABLED', 'disabled');
define('TOOL_STATUS_DRAFT', 'draft');
