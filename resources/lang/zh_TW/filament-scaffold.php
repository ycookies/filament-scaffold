<?php

return [
    // Navigation
    'navigation.group' => '開發工具',
    'navigation.label' => '代碼生成器',
    'model_label' => '代碼生成器',
    'plural_model_label' => '代碼生成器',

    // Form Sections
    'section.table_resource_info' => '表與資源信息',
    'section.generation_options' => '生成選項',
    'section.table_structure' => '表結構',
    'section.migration_features' => '遷移附加功能',

    // Form Fields
    'field.table_name' => '表名',
    'field.choose_table' => '選擇表',
    'field.model' => '模型',
    'field.resource' => '資源',
    'field.field_name' => '欄位名',
    'field.translation' => '翻譯',
    'field.type' => '類型',
    'field.nullable' => '可空',
    'field.key' => '鍵',
    'field.default' => '預設值',
    'field.comment' => '註釋',

    // Generation Options
    'option.create_resource' => '創建資源',
    'option.create_model' => '創建模型',
    'option.simple_resource' => '簡單資源',
    'option.simple_resource_label' => '簡單（模態類型）資源',
    'option.create_migration' => '創建遷移',
    'option.create_factory' => '創建工廠',
    'option.create_controller' => '創建控制器',
    'option.run_migrate' => '運行遷移',
    'option.create_route' => '創建路由',
    'option.create_policy' => '創建策略',
    'option.create_api' => '創建 API',

    // Migration Features
    'migration.created_updated_at' => '創建與更新時間戳',
    'migration.created_updated_at_label' => 'Created_at & Updated_at 時間戳',
    'migration.soft_delete' => '軟刪除',
    'migration.soft_delete_label' => '軟刪除（回收站）',

    // Key Options
    'key.null' => 'NULL',
    'key.primary' => '主鍵',
    'key.unique' => '唯一',
    'key.index' => '索引',

    // Type Options
    'type.string' => '字串',
    'type.integer' => '整數',
    'type.big_integer' => '大整數',
    'type.text' => '文字',
    'type.float' => '浮點數',
    'type.double' => '雙精度',
    'type.decimal' => '小數',
    'type.boolean' => '布林值',
    'type.date' => '日期',
    'type.time' => '時間',
    'type.datetime' => '日期時間',
    'type.timestamp' => '時間戳',
    'type.char' => '字元',
    'type.medium_text' => '中等文字',
    'type.long_text' => '長文字',
    'type.tiny_integer' => '微整數',
    'type.small_integer' => '小整數',
    'type.medium_integer' => '中整數',
    'type.json' => 'JSON',
    'type.jsonb' => 'JSONB',
    'type.binary' => '二進制',
    'type.enum' => '枚舉',
    'type.ip_address' => 'IP 地址',
    'type.mac_address' => 'MAC 地址',

    // Notifications
    'notification.policy_created_title' => '包含策略的代碼生成成功！',
    'notification.policy_created_body' => '已成功為您的模型創建策略文件。請配置新策略的權限。',
    'notification.policy_action_configure' => '配置權限',
    'notification.policy_action_close' => '關閉',
    'notification.scaffold_created_title' => '代碼生成成功',
    'notification.scaffold_created_body' => '代碼生成資源已成功創建。',
    'notification.api_created_title' => 'API 服務創建成功！',
    'notification.api_created_body' => '已為以下資源生成 API 服務：<b>:resource</b><br><br>生成的文件位置：<br><b>:location</b><br><br><small><pre>:output</pre></small>',
    'notification.api_issue_title' => 'API 服務生成問題',
    'notification.api_issue_body' => '生成以下資源的 API 服務時出現問題：<b>:resource</b><br><br><small><pre>:output</pre></small>',
    'notification.api_failed_title' => 'API 服務生成失敗',
    'notification.api_failed_body' => '意外錯誤：:error',
    'notification.resource_not_found_title' => '未找到資源類',
    'notification.resource_not_found_body' => '類 `:class` 不存在。請先生成資源。',
    'notification.resource_missing_title' => '缺少資源輸入',
    'notification.resource_missing_body' => '生成 API 服務需要資源輸入。',
    'notification.error_running_commands_title' => '運行命令時出錯',
    'notification.error_running_commands_body' => '請查看日誌以獲取更多詳細信息。',

    // Action Labels
    'action.create_scaffold' => '創建代碼生成',
    'action.confirm_creation_heading' => '確認代碼生成',
    'action.confirm_creation_description' => '請確保所有代碼生成組件（資源名稱、表名等）已設置，並且創建過程<b>不會被中斷</b>。<br><br>您準備好創建代碼生成了嗎？',
    'action.yes_confirm' => '是，確認',
    'action.no_cancel' => '否，取消',
];
