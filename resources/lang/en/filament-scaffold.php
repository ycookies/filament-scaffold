<?php

return [
    // Navigation
    'navigation.group' => 'System',
    'navigation.label' => 'Scaffold Manager',
    'model_label' => 'Scaffold',
    'plural_model_label' => 'Scaffold',

    // Form Sections
    'section.table_resource_info' => 'Table & Resource Information',
    'section.generation_options' => 'Generation Options',
    'section.table_structure' => 'Table Structure',
    'section.migration_features' => 'Migration Additional Features',

    // Form Fields
    'field.table_name' => 'Table Name',
    'field.choose_table' => 'Choose Table',
    'field.model' => 'Model',
    'field.resource' => 'Resource',
    'field.field_name' => 'Field Name',
    'field.translation' => 'Translation',
    'field.type' => 'Type',
    'field.nullable' => 'Nullable',
    'field.key' => 'Key',
    'field.default' => 'Default',
    'field.comment' => 'Comment',

    // Generation Options
    'option.create_resource' => 'Create Resource',
    'option.create_model' => 'Create Model',
    'option.simple_resource' => 'Simple Resource',
    'option.simple_resource_label' => 'Simple (Modal Type) Resource',
    'option.create_migration' => 'Create Migration',
    'option.create_factory' => 'Create Factory',
    'option.create_controller' => 'Create Controller',
    'option.run_migrate' => 'Run Migrate',
    'option.create_route' => 'Create Route',
    'option.create_policy' => 'Create Policy',
    'option.create_api' => 'Create API',

    // Migration Features
    'migration.created_updated_at' => 'Created_at & Updated_at',
    'migration.created_updated_at_label' => 'Created_at & Updated_at timestamps',
    'migration.soft_delete' => 'Soft Delete',
    'migration.soft_delete_label' => 'Soft Delete (recycle bin)',

    // Key Options
    'key.null' => 'NULL',
    'key.primary' => 'Primary',
    'key.unique' => 'Unique',
    'key.index' => 'Index',

    // Type Options
    'type.string' => 'string',
    'type.integer' => 'integer',
    'type.big_integer' => 'bigInteger',
    'type.text' => 'text',
    'type.float' => 'float',
    'type.double' => 'double',
    'type.decimal' => 'decimal',
    'type.boolean' => 'boolean',
    'type.date' => 'date',
    'type.time' => 'time',
    'type.datetime' => 'dateTime',
    'type.timestamp' => 'timestamp',
    'type.char' => 'char',
    'type.medium_text' => 'mediumText',
    'type.long_text' => 'longText',
    'type.tiny_integer' => 'tinyInteger',
    'type.small_integer' => 'smallInteger',
    'type.medium_integer' => 'mediumInteger',
    'type.json' => 'json',
    'type.jsonb' => 'jsonb',
    'type.binary' => 'binary',
    'type.enum' => 'enum',
    'type.ip_address' => 'ipAddress',
    'type.mac_address' => 'macAddress',

    // Notifications
    'notification.policy_created_title' => 'Scaffold with Policy Created Successfully!',
    'notification.policy_created_body' => 'A new policy file has been successfully created for your model. Please configure the permissions for the new policy.',
    'notification.policy_action_configure' => 'Configure Permissions',
    'notification.policy_action_close' => 'Close',
    'notification.scaffold_created_title' => 'Scaffold created',
    'notification.scaffold_created_body' => 'The scaffold resource has been created successfully.',
    'notification.api_created_title' => 'API Service Created Successfully!',
    'notification.api_created_body' => 'API service has been generated for: <b>:resource</b><br><br>Generated files location:<br><b>:location</b><br><br><small><pre>:output</pre></small>',
    'notification.api_issue_title' => 'API Service Generation Issue',
    'notification.api_issue_body' => 'There was an issue generating the API service for: <b>:resource</b><br><br><small><pre>:output</pre></small>',
    'notification.api_failed_title' => 'API Service Generation Failed',
    'notification.api_failed_body' => 'Unexpected error: :error',
    'notification.resource_not_found_title' => 'Resource Class Not Found',
    'notification.resource_not_found_body' => 'The class `:class` does not exist. Please generate the resource first.',
    'notification.resource_missing_title' => 'Missing Resource Input',
    'notification.resource_missing_body' => 'The Resource input is required to generate the API service.',
    'notification.error_running_commands_title' => 'Error running commands',
    'notification.error_running_commands_body' => 'Check logs for more details.',

    // Action Labels
    'action.create_scaffold' => 'Create Scaffold',
    'action.confirm_creation_heading' => 'Confirm Scaffold Creation',
    'action.confirm_creation_description' => 'Ensure all scaffold components (resource name, table name, etc.) are set and their creation <b>won\'t be interrupted</b>.<br><br>Are you ready to create the scaffold?',
    'action.yes_confirm' => 'Yes, Confirm',
    'action.no_cancel' => 'No, Cancel',
];
