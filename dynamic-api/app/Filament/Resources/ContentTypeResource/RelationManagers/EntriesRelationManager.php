<?php

namespace App\Filament\Resources\ContentTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    protected static ?string $title = 'Submitted Entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\KeyValue::make('data')
                    ->label('Entry Data')
                    ->keyLabel('Field')
                    ->valueLabel('Value')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        // Get the content type to access its fields
        $contentType = $this->getOwnerRecord();
        $columns = [];
        
        // Add Entry ID column first
        $columns[] = Tables\Columns\TextColumn::make('id')
            ->label('Entry ID')
            ->sortable()
            ->width(80);
        
        // Dynamically create columns for each form field
        if ($contentType && $contentType->fields) {
            foreach ($contentType->fields as $field) {
                $columns[] = Tables\Columns\TextColumn::make("data.{$field->name}")
                    ->label($field->label ?: ucfirst($field->name))
                    ->getStateUsing(function ($record) use ($field) {
                        $data = $record->data;
                        if (is_array($data) && isset($data[$field->name])) {
                            $value = $data[$field->name];
                            
                            // Format value based on field type
                            switch ($field->type) {
                                case 'boolean':
                                    return $value ? 'Yes' : 'No';
                                case 'date':
                                    return is_string($value) ? date('M d, Y', strtotime($value)) : $value;
                                case 'email':
                                    return $value;
                                case 'integer':
                                    return number_format($value);
                                case 'checkbox':
                                    if (is_array($value)) {
                                        return implode(', ', $value);
                                    }
                                    return $value;
                                case 'radio':
                                case 'select':
                                    // Try to find the label for the selected value
                                    if ($field->options && is_array($field->options)) {
                                        foreach ($field->options as $option) {
                                            if ($option['value'] === $value) {
                                                return $option['label'];
                                            }
                                        }
                                    }
                                    return $value;
                                default:
                                    // Truncate long text
                                    if (is_string($value) && strlen($value) > 50) {
                                        return substr($value, 0, 50) . '...';
                                    }
                                    return $value;
                            }
                        }
                        return '-';
                    })
                    ->tooltip(function ($record) use ($field) {
                        $data = $record->data;
                        if (is_array($data) && isset($data[$field->name])) {
                            $value = $data[$field->name];
                            if (is_string($value) && strlen($value) > 50) {
                                return $value; // Show full text in tooltip
                            }
                        }
                        return null;
                    })
                    ->searchable()
                    ->sortable()
                    ->wrap();
            }
        }
        
        // Add timestamp columns
        $columns[] = Tables\Columns\TextColumn::make('created_at')
            ->label('Submitted At')
            ->dateTime('M d, Y H:i')
            ->sortable()
            ->width(140);
        
        // Add a "View All Data" action column for entries with extra fields
        $columns[] = Tables\Columns\TextColumn::make('extra_data')
            ->label('Extra Fields')
            ->getStateUsing(function ($record) use ($contentType) {
                $data = $record->data;
                if (!is_array($data)) return null;
                
                $definedFields = $contentType->fields->pluck('name')->toArray();
                $extraFields = array_diff(array_keys($data), $definedFields);
                
                if (count($extraFields) > 0) {
                    return '+' . count($extraFields) . ' more';
                }
                return null;
            })
            ->badge()
            ->color('warning')
            ->tooltip(function ($record) use ($contentType) {
                $data = $record->data;
                if (!is_array($data)) return null;
                
                $definedFields = $contentType->fields->pluck('name')->toArray();
                $extraData = array_diff_key($data, array_flip($definedFields));
                
                if (count($extraData) > 0) {
                    return 'Extra fields: ' . json_encode($extraData, JSON_PRETTY_PRINT);
                }
                return null;
            })
            ->toggleable(isToggledHiddenByDefault: true)
            ->width(100);
        
        $columns[] = Tables\Columns\TextColumn::make('updated_at')
            ->label('Updated At')
            ->dateTime('M d, Y H:i')
            ->sortable()
            ->width(140)
            ->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->recordTitleAttribute('id')
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('created_at')
                    ->label('Submitted Date')
                    ->options([
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            switch ($data['value']) {
                                case 'today':
                                    $query->whereDate('created_at', today());
                                    break;
                                case 'week':
                                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                                    break;
                                case 'month':
                                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                                    break;
                            }
                        }
                    }),
            ])
            ->headerActions([
                // Remove create action as entries are created via API
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(function ($record) {
                        return view('filament.content-entry-view', [
                            'entry' => $record,
                            'contentType' => $record->contentType,
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
