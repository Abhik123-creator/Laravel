<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentTypeResource\Pages;
use App\Filament\Resources\ContentTypeResource\RelationManagers;
use App\Models\ContentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ContentTypeResource extends Resource
{
    protected static ?string $model = ContentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Content Type Configuration')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ContentType::class, 'slug', ignoreRecord: true)
                                    ->rules(['alpha_dash']),
                                
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(500)
                                    ->rows(3)
                                    ->placeholder('Brief description of this form'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Settings')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText('When disabled, the API endpoint will not accept new submissions')
                                    ->default(true),
                                
                                Forms\Components\Toggle::make('require_authentication')
                                    ->label('Require Authentication')
                                    ->helperText('Require API authentication to submit entries')
                                    ->default(false),
                                
                                Forms\Components\Toggle::make('captcha_enabled')
                                    ->label('Enable Captcha')
                                    ->helperText('Require users to solve a captcha before submitting the form')
                                    ->default(false)
                                    ->live(),
                                
                                Forms\Components\Select::make('captcha_difficulty')
                                    ->label('Captcha Difficulty')
                                    ->options([
                                        'easy' => 'Easy (Simple addition: 5 + 3)',
                                        'medium' => 'Medium (Mixed operations: 15 - 7)',
                                        'hard' => 'Hard (Complex: (12 + 8) × 2)',
                                    ])
                                    ->default('medium')
                                    ->helperText('Set the difficulty level for the captcha')
                                    ->visible(fn (Forms\Get $get) => $get('captcha_enabled')),
                                
                                Forms\Components\TextInput::make('api_rate_limit')
                                    ->label('API Rate Limit')
                                    ->numeric()
                                    ->default(100)
                                    ->helperText('Maximum API calls per hour')
                                    ->suffixIcon('heroicon-o-clock'),
                                
                                Forms\Components\KeyValue::make('settings')
                                    ->label('Custom Settings')
                                    ->keyLabel('Setting Name')
                                    ->valueLabel('Setting Value')
                                    ->helperText('Additional custom settings for this form'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Integration')
                            ->schema([
                                Forms\Components\Section::make('Available Endpoints')
                                    ->description('Use these endpoints to integrate this form into any website or application')
                                    ->schema([
                                        Forms\Components\Placeholder::make('web_endpoints')
                                            ->label('Web Form URLs')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see integration URLs';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <div class='p-3 bg-blue-50 rounded-lg border border-blue-200'>
                                                            <h4 class='font-semibold text-blue-900 mb-2'>📋 Direct Form Page</h4>
                                                            <code class='block p-2 bg-white rounded border text-sm text-gray-800'>{$baseUrl}/forms/{$slug}</code>
                                                            <p class='text-sm text-blue-700 mt-1'>Full webpage with form - perfect for direct links</p>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-green-50 rounded-lg border border-green-200'>
                                                            <h4 class='font-semibold text-green-900 mb-2'>🖼️ Embeddable Form (Iframe)</h4>
                                                            <code class='block p-2 bg-white rounded border text-sm text-gray-800'>{$baseUrl}/embed/forms/{$slug}</code>
                                                            <p class='text-sm text-green-700 mt-1'>Optimized for iframe embedding in other websites</p>
                                                        </div>
                                                    </div>
                                                ");
                                            }),
                                        
                                        Forms\Components\Placeholder::make('api_endpoints')
                                            ->label('API Endpoints')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see API endpoints';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <div class='p-3 bg-purple-50 rounded-lg border border-purple-200'>
                                                            <h4 class='font-semibold text-purple-900 mb-2'>📊 Get Form Details</h4>
                                                            <code class='block p-2 bg-white rounded border text-sm text-gray-800'>GET {$baseUrl}/api/forms/{$slug}</code>
                                                            <p class='text-sm text-purple-700 mt-1'>Returns form structure, fields, and validation rules</p>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-orange-50 rounded-lg border border-orange-200'>
                                                            <h4 class='font-semibold text-orange-900 mb-2'>📤 Submit Form Data</h4>
                                                            <code class='block p-2 bg-white rounded border text-sm text-gray-800'>POST {$baseUrl}/api/forms/{$slug}/entries</code>
                                                            <p class='text-sm text-orange-700 mt-1'>Submit form data as JSON via API</p>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-red-50 rounded-lg border border-red-200'>
                                                            <h4 class='font-semibold text-red-900 mb-2'>🔒 Get Captcha</h4>
                                                            <code class='block p-2 bg-white rounded border text-sm text-gray-800'>GET {$baseUrl}/api/captcha/{$slug}</code>
                                                            <p class='text-sm text-red-700 mt-1'>Get captcha image and ID for API submissions</p>
                                                        </div>
                                                    </div>
                                                ");
                                            }),
                                    ]),
                                
                                Forms\Components\Section::make('Integration Examples')
                                    ->description('Copy and paste these examples to integrate your form')
                                    ->schema([
                                        Forms\Components\Placeholder::make('direct_link')
                                            ->label('1. Direct Link Integration')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see examples';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                $formName = $record->name ?? 'Form';
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <h5 class='font-medium text-gray-900'>Simple Link:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>&lt;a href=\"{$baseUrl}/forms/{$slug}\" target=\"_blank\"&gt;
  Fill Out {$formName}
&lt;/a&gt;</pre>
                                                        
                                                        <h5 class='font-medium text-gray-900'>Button Style:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>&lt;a href=\"{$baseUrl}/forms/{$slug}\" 
   class=\"btn btn-primary\" 
   target=\"_blank\"&gt;
  Open {$formName}
&lt;/a&gt;</pre>
                                                    </div>
                                                ");
                                            }),
                                        
                                        Forms\Components\Placeholder::make('iframe_integration')
                                            ->label('2. Iframe Integration')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see examples';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <h5 class='font-medium text-gray-900'>Basic Iframe:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>&lt;iframe 
  src=\"{$baseUrl}/embed/forms/{$slug}\" 
  width=\"100%\" 
  height=\"800\" 
  frameborder=\"0\"
  style=\"border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);\"&gt;
&lt;/iframe&gt;</pre>
                                                        
                                                        <h5 class='font-medium text-gray-900'>Responsive Iframe:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>&lt;div style=\"position: relative; width: 100%; height: 0; padding-bottom: 100%;\"&gt;
  &lt;iframe src=\"{$baseUrl}/embed/forms/{$slug}\"
          style=\"position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;\"&gt;
  &lt;/iframe&gt;
&lt;/div&gt;</pre>
                                                    </div>
                                                ");
                                            }),
                                        
                                        Forms\Components\Placeholder::make('api_integration')
                                            ->label('3. JavaScript API Integration')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see examples';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <h5 class='font-medium text-gray-900'>Get Form Structure:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>// Get form details
const form = await fetch('{$baseUrl}/api/forms/{$slug}')
  .then(response => response.json());

console.log(form.form.fields); // Available fields</pre>
                                                        
                                                        <h5 class='font-medium text-gray-900'>Submit Form Data:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>// Submit form data
const result = await fetch('{$baseUrl}/api/forms/{$slug}/entries', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    // Add your form data here
    field_name: 'value',
    email: 'user@example.com'
  })
});

const response = await result.json();
console.log(response); // Success/error response</pre>
                                                    </div>
                                                ");
                                            }),
                                        
                                        Forms\Components\Placeholder::make('wordpress_integration')
                                            ->label('4. WordPress Shortcode')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see examples';
                                                }
                                                
                                                $slug = $record->slug;
                                                $formName = $record->name ?? 'Form';
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-3'>
                                                        <h5 class='font-medium text-gray-900'>Add to functions.php:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>function dynamic_form_shortcode(\$atts) {
    \$atts = shortcode_atts(array(
        'slug' => '{$slug}',
        'type' => 'iframe',
        'width' => '100%',
        'height' => '800',
        'domain' => 'yourdomain.com'
    ), \$atts);
    
    if (\$atts['type'] === 'link') {
        return sprintf(
            '&lt;a href=\"https://%s/forms/%s\" target=\"_blank\"&gt;%s&lt;/a&gt;',
            \$atts['domain'], \$atts['slug'], '{$formName}'
        );
    }
    
    return sprintf(
        '&lt;iframe src=\"https://%s/embed/forms/%s\" width=\"%s\" height=\"%s\"&gt;&lt;/iframe&gt;',
        \$atts['domain'], \$atts['slug'], \$atts['width'], \$atts['height']
    );
}
add_shortcode('dynamic_form', 'dynamic_form_shortcode');</pre>
                                                        
                                                        <h5 class='font-medium text-gray-900'>Use in WordPress:</h5>
                                                        <pre class='p-3 bg-gray-100 rounded text-sm overflow-x-auto text-gray-800'>[dynamic_form slug=\"{$slug}\"]
[dynamic_form slug=\"{$slug}\" type=\"link\"]</pre>
                                                    </div>
                                                ");
                                            }),
                                    ]),
                                
                                Forms\Components\Section::make('Field Types & Validation')
                                    ->description('Available field types and their validation rules')
                                    ->schema([
                                        Forms\Components\Placeholder::make('field_types')
                                            ->label('Supported Field Types')
                                            ->content(function ($record) {
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='grid grid-cols-2 gap-4'>
                                                        <div class='space-y-2'>
                                                            <div class='p-2 bg-blue-50 rounded border border-blue-200'>
                                                                <strong class='text-blue-900'>text</strong> <span class='text-gray-700'>- Multi-line text area</span>
                                                            </div>
                                                            <div class='p-2 bg-green-50 rounded border border-green-200'>
                                                                <strong class='text-green-900'>email</strong> <span class='text-gray-700'>- Email input with validation</span>
                                                            </div>
                                                            <div class='p-2 bg-yellow-50 rounded border border-yellow-200'>
                                                                <strong class='text-yellow-900'>integer</strong> <span class='text-gray-700'>- Number input</span>
                                                            </div>
                                                            <div class='p-2 bg-purple-50 rounded border border-purple-200'>
                                                                <strong class='text-purple-900'>date</strong> <span class='text-gray-700'>- Date picker</span>
                                                            </div>
                                                        </div>
                                                        <div class='space-y-2'>
                                                            <div class='p-2 bg-pink-50 rounded border border-pink-200'>
                                                                <strong class='text-pink-900'>boolean</strong> <span class='text-gray-700'>- Checkbox (true/false)</span>
                                                            </div>
                                                            <div class='p-2 bg-indigo-50 rounded border border-indigo-200'>
                                                                <strong class='text-indigo-900'>radio</strong> <span class='text-gray-700'>- Radio buttons (single select)</span>
                                                            </div>
                                                            <div class='p-2 bg-orange-50 rounded border border-orange-200'>
                                                                <strong class='text-orange-900'>checkbox</strong> <span class='text-gray-700'>- Multiple checkboxes</span>
                                                            </div>
                                                            <div class='p-2 bg-red-50 rounded border border-red-200'>
                                                                <strong class='text-red-900'>select</strong> <span class='text-gray-700'>- Dropdown menu</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ");
                                            }),
                                        
                                        Forms\Components\Placeholder::make('validation_info')
                                            ->label('Validation Rules')
                                            ->content(function ($record) {
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='space-y-2'>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Required fields</strong> <span class='text-gray-700'>are automatically validated</span>
                                                        </div>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Email fields</strong> <span class='text-gray-700'>validate proper email format</span>
                                                        </div>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Integer fields</strong> <span class='text-gray-700'>accept numbers only</span>
                                                        </div>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Date fields</strong> <span class='text-gray-700'>validate date format</span>
                                                        </div>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Option fields</strong> <span class='text-gray-700'>(radio/checkbox/select) validate against available options</span>
                                                        </div>
                                                        <div class='p-2 bg-gray-50 rounded border border-gray-200'>
                                                            <span class='text-green-600'>✅</span> <strong class='text-gray-900'>Captcha validation</strong> <span class='text-gray-700'>if enabled</span>
                                                        </div>
                                                    </div>
                                                ");
                                            }),
                                    ]),
                                
                                Forms\Components\Section::make('Quick Reference')
                                    ->description('Quick copy-paste URLs for immediate use')
                                    ->schema([
                                        Forms\Components\Placeholder::make('quick_urls')
                                            ->label('Ready-to-Use URLs')
                                            ->content(function ($record) {
                                                if (!$record || !$record->slug) {
                                                    return 'Save the form first to see URLs';
                                                }
                                                
                                                $baseUrl = config('app.url');
                                                $slug = $record->slug;
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                                                        <div class='p-3 bg-blue-50 rounded border border-blue-200'>
                                                            <h5 class='font-medium text-blue-900 mb-2'>🔗 Direct Link</h5>
                                                            <input type='text' value='{$baseUrl}/forms/{$slug}' 
                                                                   class='w-full p-2 text-sm border rounded bg-white text-gray-800' readonly 
                                                                   onclick='this.select(); document.execCommand(\"copy\"); alert(\"Copied to clipboard!\");'>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-green-50 rounded border border-green-200'>
                                                            <h5 class='font-medium text-green-900 mb-2'>📱 Embed URL</h5>
                                                            <input type='text' value='{$baseUrl}/embed/forms/{$slug}' 
                                                                   class='w-full p-2 text-sm border rounded bg-white text-gray-800' readonly 
                                                                   onclick='this.select(); document.execCommand(\"copy\"); alert(\"Copied to clipboard!\");'>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-purple-50 rounded border border-purple-200'>
                                                            <h5 class='font-medium text-purple-900 mb-2'>🔌 API Details</h5>
                                                            <input type='text' value='{$baseUrl}/api/forms/{$slug}' 
                                                                   class='w-full p-2 text-sm border rounded bg-white text-gray-800' readonly 
                                                                   onclick='this.select(); document.execCommand(\"copy\"); alert(\"Copied to clipboard!\");'>
                                                        </div>
                                                        
                                                        <div class='p-3 bg-orange-50 rounded border border-orange-200'>
                                                            <h5 class='font-medium text-orange-900 mb-2'>📤 API Submit</h5>
                                                            <input type='text' value='{$baseUrl}/api/forms/{$slug}/entries' 
                                                                   class='w-full p-2 text-sm border rounded bg-white text-gray-800' readonly 
                                                                   onclick='this.select(); document.execCommand(\"copy\"); alert(\"Copied to clipboard!\");'>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class='mt-4 p-3 bg-yellow-50 rounded border border-yellow-200'>
                                                        <p class='text-sm text-yellow-800'>
                                                            💡 <strong>Tip:</strong> Click any URL to copy it to your clipboard!
                                                        </p>
                                                    </div>
                                                ");
                                            }),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->placeholder('No description')
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('entries_count')
                    ->counts('entries')
                    ->label('Entries')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fields_count')
                    ->counts('fields')
                    ->label('Fields')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All forms')
                    ->trueLabel('Active forms only')
                    ->falseLabel('Inactive forms only'),
                
                Tables\Filters\TernaryFilter::make('require_authentication')
                    ->label('Authentication Required')
                    ->placeholder('All forms')
                    ->trueLabel('Auth required')
                    ->falseLabel('Public forms'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FieldsRelationManager::class,
            RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentTypes::route('/'),
            'create' => Pages\CreateContentType::route('/create'),
            'edit' => Pages\EditContentType::route('/{record}/edit'),
        ];
    }
}
