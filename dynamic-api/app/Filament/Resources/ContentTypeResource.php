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
