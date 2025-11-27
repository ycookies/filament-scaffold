<?php

namespace FilamentAdmin\FilamentScaffold\Resources;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use FilamentAdmin\FilamentScaffold\Resources\ScaffoldResource\Pages;

if (! defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

class ScaffoldResource extends Resource
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::CubeTransparent;

    /********************************************
     * Group name in the 'navigation bar'
     * @var string|null
     */
    protected static string | \UnitEnum | null $navigationGroup = null;

    /********************************************
     * Plural label for the resource
     * @var string|null
     */
    protected static ?string $pluralModelLabel = null;

    protected static ?string $navigationLabel = null;

    /********************************************
     * Singular label for the resource
     * @var string|null
     */
    protected static ?string $modelLabel = null;

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup ?? __('filament-scaffold::filament-scaffold.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? __('filament-scaffold::filament-scaffold.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('filament-scaffold::filament-scaffold.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('filament-scaffold::filament-scaffold.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /********************************************
                 * TABLE NAME, MODEL NAME, RESOURCE NAME
                 */
                Section::make(__('filament-scaffold::filament-scaffold.section.table_resource_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make('Table Name')
                                    ->label(__('filament-scaffold::filament-scaffold.field.table_name'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $modelName = str_replace('_', '', ucwords($state, '_'));
                                        $set('Model', 'app\\Models\\' . $modelName);
                                        $set('Resource', 'app\\Filament\\Resources\\' . $modelName . 'Resource');
                                        $set('Choose Table', $state);
                                    })
                                    ->required(),

                                Forms\Components\Select::make('Choose Table')
                                    ->label(__('filament-scaffold::filament-scaffold.field.choose_table'))
                                    ->options(self::getAllTableNames())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $allTables = self::getAllTableNames();

                                        if (! isset($allTables[$state])) {
                                            return;
                                        }

                                        $tableName    = $allTables[$state];
                                        $tableColumns = self::getTableColumns($tableName);
                                        $modelName    = Str::singular(str_replace('_', '', ucwords($tableName, '_')));
                                        $set('Table Name', $tableName);
                                        $set('Model', 'app\\Models\\' . $modelName);
                                        $set('Resource', 'app\\Filament\\Resources\\' . $modelName . 'Resource');
                                        $set('Table', $tableColumns);
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('Model')
                                    ->label(__('filament-scaffold::filament-scaffold.field.model'))
                                    ->default('app\\Models\\')
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('Resource')
                                    ->label(__('filament-scaffold::filament-scaffold.field.resource'))
                                    ->default('app\\Filament\\Resources\\')
                                    ->live(onBlur: true),
                            ]),
                    ])
                    ->columnSpan(2),

                /********************************************
                 * GENERATION OPTIONS
                 */
                Section::make(__('filament-scaffold::filament-scaffold.section.generation_options'))
                    ->schema([
                        Forms\Components\Checkbox::make('Create Resource')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_resource'))
                            ->default(true),
                        Forms\Components\Checkbox::make('Create Model')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_model'))
                            ->default(true),
                        Forms\Components\Checkbox::make('Simple Resource')
                            ->default(false)
                            ->label(__('filament-scaffold::filament-scaffold.option.simple_resource_label')),
                        Forms\Components\Checkbox::make('Create Migration')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_migration')),
                        Forms\Components\Checkbox::make('Create Factory')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_factory')),
                        Forms\Components\Checkbox::make('Create Controller')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_controller')),
                        Forms\Components\Checkbox::make('Run Migrate')
                            ->label(__('filament-scaffold::filament-scaffold.option.run_migrate')),
                        Forms\Components\Checkbox::make('Create Route')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_route')),
                        Forms\Components\Checkbox::make('Create Policy')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_policy'))
                            ->default(false)
                            ->hidden(fn () => ! class_exists(\BezhanSalleh\FilamentShield\FilamentShield::class)),
                        Forms\Components\Checkbox::make('create_api')
                            ->label(__('filament-scaffold::filament-scaffold.option.create_api'))
                            ->default(false)
                            ->hidden(fn () => ! class_exists(\Rupadana\ApiService\ApiService::class)),

                    ])
                    ->columns(2)
                    ->columnSpan(1),

                /********************************************
                 * TABLE STRUCTURE
                 */
                Section::make(__('filament-scaffold::filament-scaffold.section.table_structure'))
                    ->schema([
                        Forms\Components\Repeater::make('Table')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-scaffold::filament-scaffold.field.field_name'))
                                    ->required()
                                    ->default(fn ($record) => $record['name'] ?? ''),
                                Forms\Components\TextInput::make('translation')
                                    ->label(__('filament-scaffold::filament-scaffold.field.translation')),
                                Forms\Components\Select::make('type')
                                    ->label(__('filament-scaffold::filament-scaffold.field.type'))
                                    ->native(false)
                                    ->searchable()
                                    ->options([
                                        'string'        => __('filament-scaffold::filament-scaffold.type.string'),
                                        'integer'       => __('filament-scaffold::filament-scaffold.type.integer'),
                                        'bigInteger'    => __('filament-scaffold::filament-scaffold.type.big_integer'),
                                        'text'          => __('filament-scaffold::filament-scaffold.type.text'),
                                        'float'         => __('filament-scaffold::filament-scaffold.type.float'),
                                        'double'        => __('filament-scaffold::filament-scaffold.type.double'),
                                        'decimal'       => __('filament-scaffold::filament-scaffold.type.decimal'),
                                        'boolean'       => __('filament-scaffold::filament-scaffold.type.boolean'),
                                        'date'          => __('filament-scaffold::filament-scaffold.type.date'),
                                        'time'          => __('filament-scaffold::filament-scaffold.type.time'),
                                        'datetime'      => __('filament-scaffold::filament-scaffold.type.datetime'),
                                        'timestamp'     => __('filament-scaffold::filament-scaffold.type.timestamp'),
                                        'char'          => __('filament-scaffold::filament-scaffold.type.char'),
                                        'mediumText'    => __('filament-scaffold::filament-scaffold.type.medium_text'),
                                        'longText'      => __('filament-scaffold::filament-scaffold.type.long_text'),
                                        'tinyInteger'   => __('filament-scaffold::filament-scaffold.type.tiny_integer'),
                                        'smallInteger'  => __('filament-scaffold::filament-scaffold.type.small_integer'),
                                        'mediumInteger' => __('filament-scaffold::filament-scaffold.type.medium_integer'),
                                        'json'          => __('filament-scaffold::filament-scaffold.type.json'),
                                        'jsonb'         => __('filament-scaffold::filament-scaffold.type.jsonb'),
                                        'binary'        => __('filament-scaffold::filament-scaffold.type.binary'),
                                        'enum'          => __('filament-scaffold::filament-scaffold.type.enum'),
                                        'ipAddress'     => __('filament-scaffold::filament-scaffold.type.ip_address'),
                                        'macAddress'    => __('filament-scaffold::filament-scaffold.type.mac_address'),
                                    ])
                                    ->default(fn ($record) => $record['type'] ?? 'string')
                                    ->live(),
                                Forms\Components\Checkbox::make('nullable')
                                    ->label(__('filament-scaffold::filament-scaffold.field.nullable'))
                                    ->inline(false)
                                    ->default(fn ($record) => $record['nullable'] ?? false),
                                Forms\Components\Select::make('key')
                                    ->label(__('filament-scaffold::filament-scaffold.field.key'))
                                    ->default('')
                                    ->options([
                                        ''        => __('filament-scaffold::filament-scaffold.key.null'),
                                        'primary' => __('filament-scaffold::filament-scaffold.key.primary'),
                                        'unique'  => __('filament-scaffold::filament-scaffold.key.unique'),
                                        'index'   => __('filament-scaffold::filament-scaffold.key.index'),
                                    ])
                                    ->default(fn ($record) => $record['key'] ?? ''),
                                Forms\Components\TextInput::make('default')
                                    ->label(__('filament-scaffold::filament-scaffold.field.default'))
                                    ->default(fn ($record) => $record['default'] ?? ''),
                                Forms\Components\Textarea::make('comment')
                                    ->label(__('filament-scaffold::filament-scaffold.field.comment'))
                                    ->autosize()
                                    ->default(fn ($record) => $record['comment'] ?? ''),
                            ])
                            ->columns(7),
                    ])
                    ->columnSpan('full'),

                /********************************************
                 * MIGRATION ADDITIONAL FEATURES
                 */
                Section::make(__('filament-scaffold::filament-scaffold.section.migration_features'))
                    ->schema([
                        Forms\Components\Checkbox::make('Created_at & Updated_at')
                            ->label(__('filament-scaffold::filament-scaffold.migration.created_updated_at_label'))
                            ->default(true)
                            ->inline(),
                        Forms\Components\Checkbox::make('Soft Delete')
                            ->label(__('filament-scaffold::filament-scaffold.migration.soft_delete_label'))
                            ->default(true)
                            ->inline(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ])
            ->columns(3);
    }

    public static function getAllTableNames(): array
    {
        $tables = DB::select('SHOW TABLES');

        return array_map('current', $tables);
    }

    public static function getTableColumns($tableName)
    {
        $columns       = DB::select('SHOW COLUMNS FROM ' . $tableName);
        $columnDetails = [];

        $typeMapping = [
            'varchar'    => 'string',
            'int'        => 'integer',
            'bigint'     => 'bigInteger',
            'text'       => 'text',
            'float'      => 'float',
            'double'     => 'double',
            'decimal'    => 'decimal',
            'bool'       => 'boolean',
            'date'       => 'date',
            'time'       => 'time',
            'datetime'   => 'dateTime',
            'timestamp'  => 'timestamp',
            'char'       => 'char',
            'mediumtext' => 'mediumText',
            'longtext'   => 'longText',
            'tinyint'    => 'tinyInteger',
            'smallint'   => 'smallInteger',
            'mediumint'  => 'mediumInteger',
            'json'       => 'json',
            'jsonb'      => 'jsonb',
            'binary'     => 'binary',
            'enum'       => 'enum',
            'ipaddress'  => 'ipAddress',
            'macaddress' => 'macAddress',
        ];

        $keyMapping = [
            'PRI' => 'primary',
            'UNI' => 'unique',
            'MUL' => 'index',
        ];

        foreach ($columns as $column) {
            if ($column->Field === 'id' || $column->Field === 'ID' || $column->Field === 'created_at' || $column->Field === 'updated_at' || $column->Field === 'deleted_at') {
                continue;
            }

            $type = preg_replace('/\(.+\)/', '', $column->Type);
            $type = preg_split('/\s+/', $type)[0];

            $key = $column->Key;

            $translatedType = $typeMapping[$type] ?? $type;
            $translatedKey  = $keyMapping[$key] ?? $key;

            $columnDetails[] = [
                'name'     => $column->Field,
                'type'     => $translatedType,
                'nullable' => $column->Null === 'YES',
                'key'      => $translatedKey,
                'default'  => $column->Default,
                'comment'  => '',
            ];
        }

        return $columnDetails;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\CreateScaffold::route('/'),
        ];
    }

    public static function getFileName($path)
    {
        $normalizedPath        = str_replace('\\', '/', $path);
        $fileNameWithExtension = basename($normalizedPath);
        $fileName              = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

        return $fileName;
    }

    public static function generateFiles(array $data)
    {
        $basePath = base_path();

        $modelName = self::getFileName($data['Model']);

        $resourceName = self::getFileName($data['Resource']);

        chdir($basePath);
        $migrationPath  = null;
        $resourcePath   = null;
        $modelPath      = null;
        $controllerPath = null;

        /********************************************
         * MIGRATION FILE
         */
        if ($data['Create Migration']) {
            Artisan::call('make:migration', [
                'name'             => 'create_' . $data['Table Name'] . '_table',
                '--no-interaction' => true,
            ]);
            $output = Artisan::output();
            if (strpos($output, 'Migration') !== false) {
                preg_match('/\[([^\]]+)\]/', $output, $matches);
                $migrationPath = $matches[1] ?? null;
            }
            self::overwriteMigrationFile($migrationPath, $data);
        }

        if ($data['Create Factory']) {
            Artisan::call('make:factory', [
                'name'             => $data['Table Name'] . 'Factory',
                '--no-interaction' => true,
            ]);
        }

        /********************************************
         * CREATE MODEL
         */
        if ($data['Create Model']) {
            Artisan::call('make:model', [
                'name'             => $modelName,
                '--no-interaction' => true,
            ]);
            $output = Artisan::output();
            if (strpos($output, 'Model') !== false) {
                preg_match('/\[([^\]]+)\]/', $output, $matches);
                $modelPath = $matches[1] ?? null;
            }
            self::overwriteModelFile($modelPath, $data);
        }

        /********************************************
         * CREATE RESOURCE FILE
         */
        if ($data['Create Resource']) {
            $command = [
                'name'             => $resourceName,
                '--generate'       => true,
                '--view'           => true,
                '--force'          => true,
                '--no-interaction' => true,
            ];

            /**************************
             * --simple (modal type)
             */
            if ($data['Simple Resource']) {
                $command['--simple'] = true;
            }

            Artisan::call('make:filament-resource', $command);
            $output = Artisan::output();
            preg_match('/\[([^\]]+)\]/', $output, $matches);
            $resourcePath = $matches[1] ?? null;
            self::overwriteResourceFile($resourcePath, $data);
        }

        /********************************************
         * CREATE CONTROLLER
         */
        if ($data['Create Controller']) {
            Artisan::call('make:controller', [
                'name'             => $data['Table Name'] . 'Controller',
                '--model'          => $modelName,
                '--resource'       => true,
                '--no-interaction' => true,
            ]);
            $output = Artisan::output();
            preg_match('/\[([^\]]+)\]/', $output, $matches);
            $controllerPath = $matches[1] ?? null;
            self::overwriteControllerFile($controllerPath, $data);
        }

        /********************************************
         * POLICY FILE (For Permissions)
         */
        if (class_exists(\BezhanSalleh\FilamentShield\FilamentShield::class)) {
            /** @phpstan-ignore-next-line */
            $url = \BezhanSalleh\FilamentShield\Resources\RoleResource::getUrl();
            if ($data['Create Policy']) {
                $modelName = self::getFileName($data['Model']);
                Artisan::call('make:policy', [
                    'name'             => $modelName . 'Policy',
                    '--model'          => $modelName,
                    '--no-interaction' => true,
                ]);
                $output = Artisan::output();
                if (strpos($output, 'Policy') !== false) {
                    preg_match('/\[([^\]]+)\]/', $output, $matches);
                    $policyPath = $matches[1] ?? null;
                    if ($policyPath) {
                        self::updatePolicyFile($policyPath, $modelName);
                        // Log::info("Policy file created and updated at: $policyPath");
                        /********************************************
                         * SUCCESS NOTIFICATION
                         */
                        Notification::make()
                            ->success()
                            ->persistent()
                            ->title(__('filament-scaffold::filament-scaffold.notification.policy_created_title'))
                            ->body(__('filament-scaffold::filament-scaffold.notification.policy_created_body'))
                            ->icon('heroicon-o-shield-check')
                            ->actions([
                                Action::make('view')
                                    ->label(__('filament-scaffold::filament-scaffold.notification.policy_action_configure'))
                                    ->button()
                                    ->url($url, shouldOpenInNewTab: true),
                                Action::make('close')
                                    ->label(__('filament-scaffold::filament-scaffold.notification.policy_action_close'))
                                    ->color('gray')
                                    ->close(),
                            ])
                            ->send();
                    }
                }
            }
        } else {
            $url = '/default-url';
        }

        /********************************************
         * EXECUTE THE CREATING OF ROUTE
         * IF Create Route is Check
         */
        if ($data['Create Route']) {
            $controllerName = self::getFileName($controllerPath);
            self::addRoutes($data, $controllerName);
        }

        /********************************************
         * AFTER FILE/DB GENERATION, RUN THIS ARTISAN COMMANDS:
         */
        $commands = [
            'cache:clear',
            'config:cache',
            'config:clear',
            'route:cache',
            'route:clear',
            'icons:cache',
            'filament:cache-components',
        ];

        $commandErrors = [];

        foreach ($commands as $command) {
            $fullCommand    = "php artisan $command";
            $descriptorspec = [
                0 => ['pipe', 'r'], // stdin
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'],  // stderr
            ];

            $process = proc_open($fullCommand, $descriptorspec, $pipes, base_path());

            if (is_resource($process)) {
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $error = stream_get_contents($pipes[2]);
                fclose($pipes[2]);
                $return_value = proc_close($process);

                if ($return_value !== 0) {
                    Log::error("Error running artisan command: $fullCommand", [
                        'error'  => $error,
                        'output' => $output,
                    ]);
                    $commandErrors[] = $fullCommand;
                }
            }
        }

        /**
         * Creates an API using the filament-api-service package if it is installed.
         * See: https://github.com/rupadana/filament-api-service
         */
        if ($data['create_api'] && class_exists(\Rupadana\ApiService\ApiService::class)) {
            $resourcePath = $data['Resource'] ?? null;

            if ($resourcePath) {
                $resourceClass     = str_replace(['/', '\\'], '\\', $resourcePath);
                $resourceClass     = preg_replace('/^app\\\\/i', 'App\\', $resourceClass);
                $resourceClassName = class_basename($resourceClass);
                $apiServiceName    = str_replace('Resource', '', $resourceClassName);

                if (class_exists($resourceClass)) {
                    try {
                        // default panel ID to skip interactive prompt
                        $defaultPanelId = \Filament\Facades\Filament::getDefaultPanel()->getId();

                        // API service generator
                        Artisan::call('make:filament-api-service', [
                            'resource'         => $apiServiceName,
                            '--panel'          => $defaultPanelId,
                            '--no-interaction' => true,
                        ]);
                        $output = Artisan::output();

                        if (str_contains($output, 'created') || str_contains($output, 'generated')) {
                            Notification::make()
                                ->success()
                                ->persistent()
                                ->title(__('filament-scaffold::filament-scaffold.notification.api_created_title'))
                                ->body(new HtmlString(__('filament-scaffold::filament-scaffold.notification.api_created_body', [
                                    'resource' => $resourceClassName,
                                    'location' => "app/Filament/Resources/{$resourceClassName}/Api",
                                    'output'   => e($output),
                                ])))
                                ->icon('heroicon-o-code-bracket')
                                ->send();
                        } else {
                            Notification::make()
                                ->warning()
                                ->persistent()
                                ->title(__('filament-scaffold::filament-scaffold.notification.api_issue_title'))
                                ->body(new HtmlString(__('filament-scaffold::filament-scaffold.notification.api_issue_body', [
                                    'resource' => $resourceClassName,
                                    'output'   => e($output),
                                ])))
                                ->icon('heroicon-o-exclamation-triangle')
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('filament-scaffold::filament-scaffold.notification.api_failed_title'))
                            ->body(__('filament-scaffold::filament-scaffold.notification.api_failed_body', [
                                'error' => $e->getMessage(),
                            ]))
                            ->send();
                    }
                } else {
                    Notification::make()
                        ->danger()
                        ->title(__('filament-scaffold::filament-scaffold.notification.resource_not_found_title'))
                        ->body(__('filament-scaffold::filament-scaffold.notification.resource_not_found_body', [
                            'class' => $resourceClass,
                        ]))
                        ->send();
                }
            } else {
                Notification::make()
                    ->danger()
                    ->title(__('filament-scaffold::filament-scaffold.notification.resource_missing_title'))
                    ->body(__('filament-scaffold::filament-scaffold.notification.resource_missing_body'))
                    ->send();
            }
        }

        if (empty($commandErrors)) {

            /********************************************
             * SUCCESS NOTIFICATION
             */
            // $resourceClickLink = "\\App\\Filament\\Resources\\" . $resourceName;
            Notification::make()
                ->success()
                ->persistent()
                ->title(__('filament-scaffold::filament-scaffold.notification.scaffold_created_title'))
                ->body(__('filament-scaffold::filament-scaffold.notification.scaffold_created_body'))
                ->icon('heroicon-o-cube-transparent')
                ->send();
        } else {
            /********************************************
             * ERROR
             */
            Notification::make()
                ->title(__('filament-scaffold::filament-scaffold.notification.error_running_commands_title'))
                ->body(__('filament-scaffold::filament-scaffold.notification.error_running_commands_body'))
                ->danger()
                ->send();
        }

    }

    public static function overwriteResourceFile($resourceFile, $data)
    {
        $modelName = self::getFileName($data['Model']);

        if (file_exists($resourceFile)) {
            $content = file_get_contents($resourceFile);

            $formSchema     = self::generateFormSchema($data);
            $tableSchema    = self::generateTableSchema($data);
            $useClassChange = <<<EOD
                use App\\Models\\$modelName;
                EOD;

            $classChange = <<<EOD
                protected static ?string \$model = $modelName::class;
                EOD;

            $formFunction = <<<EOD
                public static function form(Form \$form): Form
                    {
                        return \$form
                            ->schema([
                                $formSchema
                            ]);
                    }
                EOD;

            $tableFunction = <<<EOD
                public static function table(Table \$table): Table
                    {
                        return \$table
                            ->columns([
                                $tableSchema
                            ])
                            ->filters([
                                //
                            ])
                            ->actions([
                                Tables\Actions\ViewAction::make(),
                                Tables\Actions\EditAction::make(),
                            ])
                            ->bulkActions([
                                Tables\Actions\BulkActionGroup::make([
                                    Tables\Actions\DeleteBulkAction::make(),
                                ]),
                            ]);
                    }
                EOD;

            $content = preg_replace('/use\s+App\\\\Models\\\\.*?;/s', $useClassChange, $content);
            $content = preg_replace('/protected static\s+\?string\s+\$model\s*=\s*[^\;]+;/s', $classChange, $content);
            $content = preg_replace('/public static function form.*?{.*?}/s', $formFunction, $content);
            $content = preg_replace('/public static function table.*?{.*?}/s', $tableFunction, $content);

            file_put_contents($resourceFile, $content);
        }
    }

    public static function generateFormSchema($data)
    {
        $fields = [];
        foreach ($data['Table'] as $column) {
            $fields[] = "Forms\Components\TextInput::make('{$column['name']}')->required()";
        }

        return implode(",\n", $fields);
    }

    public static function generateTableSchema($data)
    {
        $columns = [];
        foreach ($data['Table'] as $column) {
            $columns[] = "Tables\Columns\TextColumn::make('{$column['name']}')->sortable()->searchable()";
        }

        return implode(",\n", $columns);
    }

    public static function overwriteMigrationFile($filePath, $data)
    {
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);

            $upPart     = self::generateUp($data);
            $upFunction = <<<EOD
                public function up(): void
                    {
                        Schema::create('{$data['Table Name']}', function (Blueprint \$table) {
                            \$table->id();
                            $upPart;
                    }
                EOD;

            $downFunction = <<<EOD
                public function down()
                    {
                        Schema::dropIfExists('{$data['Table Name']}');
                    }
                EOD;

            $content = preg_replace('/public function up.*?{.*?}/s', $upFunction, $content);
            $content = preg_replace('/public function down.*?{.*?}/s', $downFunction, $content);

            file_put_contents($filePath, $content);
        }
        if ($data['Run Migrate'] == true) {
            Artisan::call('migrate');
        }
    }

    public static function generateUp(array $data): string
    {
        $fields = array_map(
            fn (array $column): string => self::generateColumnDefinition($column),
            $data['Table']
        );

        if ($data['Created_at & Updated_at'] == true) {
            $fields[] = '$table->timestamps()';
        }

        if ($data['Soft Delete'] == true) {
            $fields[] = '$table->softDeletes()';
        }

        return implode(";\n", $fields);
    }

    private static function generateColumnDefinition(array $column): string
    {
        $definition = "\$table->{$column['type']}('{$column['name']}')";

        $methods = [
            'nullable' => fn (): bool => $column['nullable'] ?? false,
            'default'  => fn (): ?string => $column['default'] ?? null,
            'comment'  => fn (): ?string => $column['comment'] ?? null,
            'key'      => fn (): ?string => $column['key'] ?? null,
        ];

        foreach ($methods as $method => $condition) {
            $value = $condition();
            if ($value !== null && $value !== false) {
                $definition .= match ($method) {
                    'nullable' => '->nullable()',
                    'default'  => "->default('{$value}')",
                    'comment'  => "->comment('{$value}')",
                    'key'      => "->{$value}()",
                };
            }
        }

        return $definition;
    }

    public static function overwriteModelFile($filePath, $data)
    {
        $column = self::getColumn($data);

        if (file_exists($filePath)) {
            $content    = file_get_contents($filePath);
            $useSoftDel = <<<EOD
                use Illuminate\Database\Eloquent\Model;
                use Illuminate\Database\Eloquent\SoftDeletes;
                EOD;

            $chooseTable = <<<EOD
                use HasFactory;
                    protected \$table = '{$data['Table Name']}';
                    protected \$fillable = $column;
                EOD;

            $withSoftdel = <<<EOD
                use HasFactory;
                    use SoftDeletes;
                    protected \$table = '{$data['Table Name']}';
                    protected \$fillable = $column;
                EOD;

            if ($data['Soft Delete'] == true) {
                $content = preg_replace('/use Illuminate\\\\Database\\\\Eloquent\\\\Model;/s', $useSoftDel, $content);
                $content = preg_replace('/use HasFactory;/s', $withSoftdel, $content);
            } else {
                $content = preg_replace('/use HasFactory;/s', $chooseTable, $content);
            }
            file_put_contents($filePath, $content);
        }
    }

    public static function getColumn($data)
    {
        $fields = [];
        foreach ($data['Table'] as $column) {
            $fields[] = "{$column['name']}";
        }

        return "['" . implode("','", $fields) . "']";
    }

    public static function overwriteControllerFile($filePath, $data)
    {
        if (file_exists($filePath)) {
            $content     = file_get_contents($filePath);
            $changeIndex = <<<'EOD'
                public function index()
                    {
                        return 'This your index page';
                    }
                EOD;

            $content = preg_replace('/public function index.*?{.*?}/s', $changeIndex, $content);
            file_put_contents($filePath, $content);
        }

    }

    /********************************************
     * GENERATE ROUTE, IF ALLOWED
     */
    public static function addRoutes($data, $controllerName)
    {
        $filePath = base_path('routes\web.php');
        if (file_exists($filePath)) {
            $content      = file_get_contents($filePath);
            $useStatement = <<<EOD
                use Illuminate\Support\Facades\Route;
                use App\Http\Controllers\\$controllerName;
                EOD;

            $addRoute = <<<EOD

                Route::resource('{$data['Table Name']}', {$controllerName}::class)->only([
                    'index', 'show'
                ]);
                EOD;

            $content = preg_replace('/use Illuminate\\\\Support\\\\Facades\\\\Route;/s', $useStatement, $content);
            $content .= $addRoute;

            file_put_contents($filePath, $content);
        }
    }

    /********************************************
     * CREATE POLICY FILE, IF THERE'S A FilamentShield
     */
    public static function updatePolicyFile($filePath, $modelName)
    {

        // --- Check if FilamentShield is installed
        if (! class_exists(\BezhanSalleh\FilamentShield\FilamentShield::class)) {
            return;
        }

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);

            $modelFunctionNameVariable = Str::snake(Str::plural($modelName));
            $permissionBase            = Str::of($modelName)
                ->afterLast('\\')
                ->snake()
                ->replace('_', '::');

            $methodTemplates = [
                'import_data'            => "return \$user->can('import_data_{$permissionBase}');",
                'download_template_file' => "return \$user->can('download_template_file_{$permissionBase}');",
                'viewAny'                => "return \$user->can('view_any_{$permissionBase}');",
                'view'                   => "return \$user->can('view_{$permissionBase}');",
                'create'                 => "return \$user->can('create_{$permissionBase}');",
                'update'                 => "return \$user->can('update_{$permissionBase}');",
                'delete'                 => "return \$user->can('delete_{$permissionBase}');",
                'deleteAny'              => "return \$user->can('delete_any_{$permissionBase}');",
                'restore'                => "return \$user->can('restore_{$permissionBase}');",
                'restoreAny'             => "return \$user->can('restore_any_{$permissionBase}');",
                'forceDelete'            => "return \$user->can('force_delete_{$permissionBase}');",
                'forceDeleteAny'         => "return \$user->can('force_delete_any_{$permissionBase}');",
                'replicate'              => "return \$user->can('replicate_{$permissionBase}');",
                'reorder'                => "return \$user->can('reorder_{$permissionBase}');",
            ];

            $newMethods = '';
            foreach ($methodTemplates as $method => $returnStatement) {
                $methodSignature = "public function {$method}(User \$user" .
                    (
                        in_array($method, ['viewAny', 'create', 'deleteAny', 'restoreAny', 'forceDeleteAny', 'reorder', 'import_data', 'download_template_file'])
                        ? ''
                        : ", {$modelName} \${$modelFunctionNameVariable}"
                    ) .
                    '): bool';

                $methodBody = "    {\n        {$returnStatement}\n    }";

                $fullMethod = "\n\n    {$methodSignature}\n{$methodBody}";

                // --- Check if the method already exists
                if (strpos($content, "public function {$method}(") === false) {
                    // Method doesn't exist, add it to newMethods
                    $newMethods .= $fullMethod;
                } else {
                    // --- Method exists, update it
                    $pattern     = "/public function {$method}\([^\)]*\): bool\n\s*{\n.*?\n\s*}/s";
                    $replacement = "{$methodSignature}\n{$methodBody}";
                    $content     = preg_replace($pattern, $replacement, $content);
                }
            }

            // --- Add new methods inside the class
            $content = preg_replace('/}(\s*)$/', $newMethods . "\n}", $content);

            file_put_contents($filePath, $content);
        }
    }
}
