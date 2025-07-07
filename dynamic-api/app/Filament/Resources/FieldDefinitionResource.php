<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldDefinitionResource\Pages;
use App\Models\FieldDefinition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FieldDefinitionResource extends Resource
{
    protected static ?string $model = FieldDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('content_type_id')
                    ->relationship('contentType', 'name')
                    ->required()
                    ->label('Content Type'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Field Name'),

                Forms\Components\TextInput::make('label')
                    ->required()
                    ->label('Field Label'),

                Forms\Components\Select::make('type')
                    ->options([
                        'string' => 'Text',
                        'integer' => 'Number',
                        'boolean' => 'Yes/No',
                        'date' => 'Date',
                    ])
                    ->required()
                    ->label('Field Type'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contentType.name')->label('Content Type'),
                Tables\Columns\TextColumn::make('name')->label('Field Name'),
                Tables\Columns\TextColumn::make('label')->label('Label'),
                Tables\Columns\TextColumn::make('type')->label('Field Type'),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFieldDefinitions::route('/'),
            'create' => Pages\CreateFieldDefinition::route('/create'),
            'edit' => Pages\EditFieldDefinition::route('/{record}/edit'),
        ];
    }
}
