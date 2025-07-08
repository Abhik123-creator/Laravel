<?php

namespace App\Filament\Resources\ContentTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Machine name for the field (e.g., "age", "email")'),
                
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Human readable label (e.g., "Age", "Email Address")'),
                
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'date' => 'Date',
                        'text' => 'Text Area',
                        'email' => 'Email',
                        'radio' => 'Radio Buttons',
                        'checkbox' => 'Checkboxes (Multiple Selection)',
                        'select' => 'Dropdown Select',
                    ])
                    ->helperText('Select the field type for validation')
                    ->live(),
                
                Forms\Components\Toggle::make('required')
                    ->label('Required Field')
                    ->default(true)
                    ->helperText('Whether this field is required for form submission'),
                
                Forms\Components\Textarea::make('description')
                    ->label('Field Description')
                    ->rows(2)
                    ->helperText('Optional description or help text for users'),
                
                Forms\Components\Repeater::make('options')
                    ->label('Field Options')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Option Value')
                            ->required()
                            ->helperText('The value stored in database'),
                        Forms\Components\TextInput::make('label')
                            ->label('Option Label')
                            ->required()
                            ->helperText('The text displayed to users'),
                    ])
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['radio', 'checkbox', 'select']))
                    ->helperText('Add options for radio buttons, checkboxes, or dropdown')
                    ->defaultItems(2)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'primary',
                        'integer' => 'success',
                        'boolean' => 'warning',
                        'date' => 'danger',
                        'text' => 'secondary',
                        'email' => 'info',
                        'radio' => 'purple',
                        'checkbox' => 'orange',
                        'select' => 'blue',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('required')
                    ->boolean()
                    ->label('Required'),
                
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Options')
                    ->getStateUsing(function ($record) {
                        if (in_array($record->type, ['radio', 'checkbox', 'select']) && is_array($record->options)) {
                            return count($record->options) . ' options';
                        }
                        return '-';
                    })
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->description;
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
