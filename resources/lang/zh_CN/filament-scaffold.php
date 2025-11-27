<?php

return [
    // Navigation
    'navigation.group' => '开发工具',
    'navigation.label' => '代码生成器',
    'model_label' => '代码生成器',
    'plural_model_label' => '代码生成器',

    // Form Sections
    'section.table_resource_info' => '表与资源信息',
    'section.generation_options' => '生成选项',
    'section.table_structure' => '表结构',
    'section.migration_features' => '迁移附加功能',

    // Form Fields
    'field.table_name' => '表名',
    'field.choose_table' => '选择表',
    'field.model' => '模型',
    'field.resource' => '资源',
    'field.field_name' => '字段名',
    'field.translation' => '翻译',
    'field.type' => '类型',
    'field.nullable' => '可空',
    'field.key' => '键',
    'field.default' => '默认值',
    'field.comment' => '注释',

    // Generation Options
    'option.create_resource' => '创建资源',
    'option.create_model' => '创建模型',
    'option.simple_resource' => '简单资源',
    'option.simple_resource_label' => '简单（模态类型）资源',
    'option.create_migration' => '创建迁移',
    'option.create_factory' => '创建工厂',
    'option.create_controller' => '创建控制器',
    'option.run_migrate' => '运行迁移',
    'option.create_route' => '创建路由',
    'option.create_policy' => '创建策略',
    'option.create_api' => '创建 API',

    // Migration Features
    'migration.created_updated_at' => '创建与更新时间戳',
    'migration.created_updated_at_label' => 'Created_at & Updated_at 时间戳',
    'migration.soft_delete' => '软删除',
    'migration.soft_delete_label' => '软删除（回收站）',

    // Key Options
    'key.null' => 'NULL',
    'key.primary' => '主键',
    'key.unique' => '唯一',
    'key.index' => '索引',

    // Type Options
    'type.string' => '字符串',
    'type.integer' => '整数',
    'type.big_integer' => '大整数',
    'type.text' => '文本',
    'type.float' => '浮点数',
    'type.double' => '双精度',
    'type.decimal' => '小数',
    'type.boolean' => '布尔值',
    'type.date' => '日期',
    'type.time' => '时间',
    'type.datetime' => '日期时间',
    'type.timestamp' => '时间戳',
    'type.char' => '字符',
    'type.medium_text' => '中等文本',
    'type.long_text' => '长文本',
    'type.tiny_integer' => '微整数',
    'type.small_integer' => '小整数',
    'type.medium_integer' => '中整数',
    'type.json' => 'JSON',
    'type.jsonb' => 'JSONB',
    'type.binary' => '二进制',
    'type.enum' => '枚举',
    'type.ip_address' => 'IP 地址',
    'type.mac_address' => 'MAC 地址',

    // Notifications
    'notification.policy_created_title' => '包含策略的代码生成成功！',
    'notification.policy_created_body' => '已成功为您的模型创建策略文件。请配置新策略的权限。',
    'notification.policy_action_configure' => '配置权限',
    'notification.policy_action_close' => '关闭',
    'notification.scaffold_created_title' => '代码生成成功',
    'notification.scaffold_created_body' => '代码生成资源已成功创建。',
    'notification.api_created_title' => 'API 服务创建成功！',
    'notification.api_created_body' => '已为以下资源生成 API 服务：<b>:resource</b><br><br>生成的文件位置：<br><b>:location</b><br><br><small><pre>:output</pre></small>',
    'notification.api_issue_title' => 'API 服务生成问题',
    'notification.api_issue_body' => '生成以下资源的 API 服务时出现问题：<b>:resource</b><br><br><small><pre>:output</pre></small>',
    'notification.api_failed_title' => 'API 服务生成失败',
    'notification.api_failed_body' => '意外错误：:error',
    'notification.resource_not_found_title' => '未找到资源类',
    'notification.resource_not_found_body' => '类 `:class` 不存在。请先生成资源。',
    'notification.resource_missing_title' => '缺少资源输入',
    'notification.resource_missing_body' => '生成 API 服务需要资源输入。',
    'notification.error_running_commands_title' => '运行命令时出错',
    'notification.error_running_commands_body' => '请查看日志以获取更多详细信息。',

    // Action Labels
    'action.create_scaffold' => '创建代码生成',
    'action.confirm_creation_heading' => '确认代码生成',
    'action.confirm_creation_description' => '请确保所有代码生成组件（资源名称、表名等）已设置，并且创建过程<b>不会被中断</b>。<br><br>您准备好创建代码生成了吗？',
    'action.yes_confirm' => '是，确认',
    'action.no_cancel' => '否，取消',
];
