<?php
return [
    'app_name' => 'Noithat Store',
    // Đặt base_url = '' nếu triển khai ở gốc domain. Nếu chạy trong thư mục con,
    // ví dụ https://example.com/app, hãy đặt thành '/app' hoặc URL đầy đủ.
    'base_url' => '',
    'db' => [
        'host' => 'localhost',
        'name' => 'noithat_store',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],
    // Tài khoản admin cố định (chỉ 1)
    'admin' => [
        'email' => 'admin@noithat-store.local',
        'password' => 'Admin@123' // đổi sau khi deploy
    ]
];
